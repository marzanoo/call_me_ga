<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
    <div class="flex justify-around items-center py-3">

        {{-- Beranda --}}
        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center text-gray-600
           @if (Route::is('dashboard')) text-red-800 @endif">
            <i class="fa-solid fa-house text-xl mb-1"></i>
            <span class="text-xs font-medium">Beranda</span>
        </a>

        {{-- Lapor --}}
        <a href="{{ route('reports.index') }}"
           class="flex flex-col items-center text-gray-600
           @if (Route::is('reports.index')) text-red-800 @endif">
            <i class="fa-solid fa-clipboard-list text-xl mb-1"></i>
            <span class="text-xs font-medium">Lapor</span>
        </a>

        {{-- Riwayat --}}
        <a href=""
           class="flex flex-col items-center text-gray-600">
            <i class="fa-solid fa-history text-xl mb-1"></i>
            <span class="text-xs font-medium">Riwayat</span>
        </a>

    </div>
</nav>
