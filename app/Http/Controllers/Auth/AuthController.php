<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid username or password', 401);
        }

        if (!$user->email_verified_at) {
            return back()->with('error', 'Email not verified', 403);
        }

        $browserDeviceId = $request->device_id;

        if (!$user->device_id) {
            // if ($existingUserWithDevice && $existingUserWithDevice->id !== $user->id) {
            //     Auth::logout();
            //     return back()->with(['login_error' => 'Perangkat ini sudah digunakan oleh akun lain. Satu akun hanya bisa digunakan di satu perangkat.']);
            // }
            $user->device_id = $browserDeviceId;
            $user->save();
        } elseif ($user->device_id !== $browserDeviceId) {
            $user->device_id = $browserDeviceId;
            $user->save();
        }

        $user->last_login = now();
        $user->save();

        Auth::login($user);
        Cookie::queue(Cookie::make('device_id', $browserDeviceId, 60 * 24 * 30)); // Store device_id cookie for 30 days

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        Cookie::queue(Cookie::forget('device_id'));

        return redirect()->route('login.show')->with('success', 'Logged out successfully');
    }
}
