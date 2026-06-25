@php
    $statuses = $statuses ?? collect();
    $groupedStatuses = $statuses->groupBy('status');

    $statusColors = [
        'Menunggu' => 'bg-yellow-400',
        'Diproses' => 'bg-teal-400',
        'Ditolak' => 'bg-red-400',
        'Selesai' => 'bg-green-400',
    ];
@endphp

<div class="space-y-4">
    @foreach($groupedStatuses as $statusName => $items)
        @php($firstStatus = $items->first())
        <div class="flex gap-3">
            <div class="flex flex-col items-center">
                <div class="w-5 h-5 rounded-full {{ $statusColors[$statusName] ?? 'bg-gray-400' }} flex items-center justify-center flex-shrink-0"></div>

                @if(!$loop->last)
                    <div class="w-0.5 min-h-12 flex-1 bg-gray-300 my-1"></div>
                @endif
            </div>

            <div class="flex-1 pb-4">
                <div class="flex justify-between items-start mb-2 gap-3">
                    <span class="font-semibold text-gray-800">{{ $statusName }}</span>
                    <span class="text-xs text-gray-500">{{ $firstStatus?->created_at?->format('d F Y - H:i') }}</span>
                </div>

                @if($items->count() > 1)
                    <div class="space-y-3">
                        @foreach($items as $item)
                            <div class="border-l-2 border-gray-200 pl-3">
                                <div class="flex justify-between items-start gap-3 mb-1">
                                    <p class="text-sm text-gray-600">{{ $item->keterangan ?? 'Tidak ada keterangan tambahan.' }}</p>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $item->created_at->format('d F Y - H:i') }}</span>
                                </div>
                                @if($item->feedback)
                                    <p class="text-sm text-red-600 mt-1">Feedback: {{ $item->feedback }}</p>
                                @endif
                                @if($item->creator)
                                    <p class="text-xs text-gray-400">Diupdate oleh {{ ucwords(strtolower($item->creator->name)) }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-600">{{ $firstStatus->keterangan ?? 'Tidak ada keterangan tambahan.' }}</p>
                    @if($firstStatus->feedback)
                        <p class="text-sm text-red-600 mt-1">Feedback: {{ $firstStatus->feedback }}</p>
                    @endif
                    @if($firstStatus->creator)
                        <p class="text-xs text-gray-400 mt-1">Diupdate oleh {{ ucwords(strtolower($firstStatus->creator->name)) }}</p>
                    @endif
                @endif
            </div>
        </div>
    @endforeach
</div>
