<header class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-b from-red-700 to-red-800 text-white">
    <div class="flex items-center justify-between px-4 py-3">
        <img src="{{ asset('logo/logo_wag_white.png') }}" alt="Logo" class="h-10">


        <!-- Judul -->
        <h1 class="text-lg font-bold">Call Me GA</h1>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="ml-4">
            @csrf
            <button type="submit" class="flex items-center space-x-2 hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-white rounded">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>    
        </form>
    </div>
</header>