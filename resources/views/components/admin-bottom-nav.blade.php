<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
    <div class="flex justify-around items-center py-3">

        {{-- Beranda --}}
        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center text-gray-600
           @if (Route::is('dashboard')) text-red-800 @endif">
            <i class="fa-solid fa-house text-xl mb-1"></i>
            <span class="text-xs font-medium">Beranda</span>
        </a>

        {{-- Laporan Masuk --}}
        <a href="{{ route('admin.reports.index', ['status' => auth()->user()?->role === 2 ? 'diproses' : 'menunggu']) }}"
           class="flex flex-col items-center text-gray-600
           @if (Route::is('admin.reports.index')) text-red-800 @endif">
            <i class="fa-solid fa-clipboard-list text-xl mb-1"></i>
            <span class="text-xs font-medium">{{ auth()->user()?->role === 2 ? 'Tugas Diproses' : 'Laporan Masuk' }}</span>
        </a>

        {{-- Laporan Selesai --}}
        <a href="{{ route('admin.reports.finish.index') }}"
           class="flex flex-col items-center text-gray-600
           @if (Route::is('admin.reports.finish.index')) text-red-800 @endif">
            <i class="fa-solid fa-clipboard-check text-xl mb-1"></i>
            <span class="text-xs font-medium">Laporan Selesai</span>
        </a>

        {{-- Master --}}
        @if (auth()->user()?->role === config('callmega.roles.admin'))
            <a href="{{ route('admin.master.locations.index') }}"
               class="flex flex-col items-center text-gray-600
               @if (Route::is('admin.master.*')) text-red-800 @endif">
                <i class="fa-solid fa-sliders text-xl mb-1"></i>
                <span class="text-xs font-medium">Master</span>
            </a>
        @endif
    </div>
</nav>
