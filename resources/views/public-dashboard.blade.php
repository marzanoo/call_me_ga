<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo_wag.png') }}">
    <title>Dashboard Publik - Call Me GA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-900">
    <main class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-6">
            <div>
                <p class="text-sm font-semibold text-[#B3282D]">Call Me GA</p>
                <h1 class="text-3xl font-bold">Dashboard Monitoring Laporan</h1>
                <p class="text-sm text-gray-500 mt-1">Terakhir diperbarui: {{ now()->format('d F Y - H:i') }}</p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:min-w-[360px]">
                <div class="bg-white border border-gray-200 rounded-lg px-4 py-3 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Jam Realtime</p>
                    <p id="realtimeClock" class="text-2xl font-bold tabular-nums text-gray-900">--:--:--</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg px-4 py-3 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Refresh Dalam</p>
                    <p class="text-2xl font-bold tabular-nums text-[#B3282D]"><span id="refreshCountdown">60</span>s</p>
                </div>
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Total Laporan</p>
                <p class="text-3xl font-bold mt-2">{{ $totalLaporanCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Menunggu</p>
                <p class="text-3xl font-bold mt-2 text-yellow-700">{{ $laporanMenungguCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Sedang Dikerjakan</p>
                <p class="text-3xl font-bold mt-2 text-blue-700">{{ $laporanDiprosesCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Selesai</p>
                <p class="text-3xl font-bold mt-2 text-green-700">{{ $laporanSelesaiCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Ditolak</p>
                <p class="text-3xl font-bold mt-2 text-red-700">{{ $laporanDitolakCount }}</p>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-[2fr_1fr]">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold">Laporan Terbaru</h2>
                    <span class="text-xs text-gray-500">8 data terakhir</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left text-xs font-semibold text-gray-500 px-3 py-2">Tanggal</th>
                                <th class="text-left text-xs font-semibold text-gray-500 px-3 py-2">Pelapor</th>
                                <th class="text-left text-xs font-semibold text-gray-500 px-3 py-2">Lokasi</th>
                                <th class="text-left text-xs font-semibold text-gray-500 px-3 py-2">Status</th>
                                <th class="text-left text-xs font-semibold text-gray-500 px-3 py-2">Penangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestReports as $report)
                                @php($latestStatus = $report->detailStatusReports->first())
                                <tr class="border-b last:border-b-0">
                                    <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $report->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-3 py-3 text-sm">{{ ucwords(strtolower($report->user->name ?? '-')) }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $report->lokasi }}</td>
                                    <td class="px-3 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ $latestStatus->status ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-sm">{{ $report->assignee ? ucwords(strtolower($report->assignee->name)) : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">Belum ada laporan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h2 class="text-lg font-bold mb-4">Lokasi Terbanyak</h2>
                <div class="space-y-3">
                    @forelse($locationSummaries as $summary)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium">{{ $summary->label }}</span>
                                <span class="text-gray-500">{{ $summary->total }}</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#B3282D]" style="width: {{ max(8, min(100, ($summary->total / max(1, $totalLaporanCount)) * 100)) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada data lokasi.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
    <script>
        const refreshIntervalSeconds = 60;
        let remainingSeconds = refreshIntervalSeconds;
        const realtimeClock = document.getElementById('realtimeClock');
        const refreshCountdown = document.getElementById('refreshCountdown');

        function padTime(value) {
            return String(value).padStart(2, '0');
        }

        function updateClock() {
            const now = new Date();
            realtimeClock.textContent = [
                padTime(now.getHours()),
                padTime(now.getMinutes()),
                padTime(now.getSeconds())
            ].join(':');
        }

        function updateCountdown() {
            refreshCountdown.textContent = remainingSeconds;

            if (remainingSeconds <= 0) {
                window.location.reload();
                return;
            }

            remainingSeconds -= 1;
        }

        updateClock();
        updateCountdown();
        setInterval(updateClock, 1000);
        setInterval(updateCountdown, 1000);
    </script>
</body>
</html>
