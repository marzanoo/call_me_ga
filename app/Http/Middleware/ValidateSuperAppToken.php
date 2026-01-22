<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ValidateSuperAppToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('web')->check()) {
            return $next($request);
        }

        $token = $this->getToken($request);

        if (!$token) {
            return $this->handleUnauthorized($request);
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return $this->handleUnauthorized($request);
            }

            auth()->guard('web')->login($user, true);

            session(['login_via_superapp' => true]);

            $request->merge(['jwt_token' => $token]);

            return $next($request);
        } catch (TokenExpiredException $e) {
            return $this->handleUnauthorized($request, 'Token telah kadaluars ' . $e->getMessage());
        } catch (TokenInvalidException $e) {
            return $this->handleUnauthorized($request, 'Token tidak valid ' . $e->getMessage());
        } catch (JWTException $e) {
            return $this->handleUnauthorized($request, 'Terjadi kesalahan saat memproses token ' . $e->getMessage());
        } catch (Exception $e) {
            return $this->handleUnauthorized($request, 'Terjadi kesalahan ' . $e->getMessage());
        }
    }

    /**
     * Get token dari request
     * Priority: Cookie -> Header -> Query Parameter
     */
    private function getToken(Request $request): ?string
    {
        // 1. Cek dari cookie (injected by Flutter WebView)
        $token = $request->cookie('super_app_token');

        if ($token) {
            return $token;
        }

        // 2. Cek dari Authorization header
        $header = $request->header('Authorization');

        if ($header && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        // 3. Cek dari query parameter (fallback untuk debugging)
        $token = $request->query('token');

        if ($token) {
            return $token;
        }

        return null;
    }

    private function handleUnauthorized(Request $request, string $message = 'Silakan login untuk melanjutkan'): Response
    {
        // Jika request expect JSON (untuk API)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 401);
        }

        // Jika web request, redirect ke login dengan error message
        return redirect()->route('login')
            ->with('error', $message);
    }
}
