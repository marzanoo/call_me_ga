<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
    <div class="flex justify-around items-center py-3">
        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center text-gray-600 @if (Route::is('dashboard')) text-red-800 @endif">
            <i class="fa-solid fa-house text-xl mb-1"></i>
            <span class="text-xs font-medium">Beranda</span>
        </a>

        <a href="{{ route('teknisi.reports.processed.index') }}"
           class="flex flex-col items-center text-gray-600 @if (Route::is('teknisi.reports.processed.*')) text-red-800 @endif">
            <i class="fa-solid fa-screwdriver-wrench text-xl mb-1"></i>
            <span class="text-xs font-medium">Tugas</span>
        </a>

        <a href="{{ route('teknisi.reports.finish.index') }}"
           class="flex flex-col items-center text-gray-600 @if (Route::is('teknisi.reports.finish.*')) text-red-800 @endif">
            <i class="fa-solid fa-clipboard-check text-xl mb-1"></i>
            <span class="text-xs font-medium">Riwayat</span>
        </a>
    </div>
</nav>
