<div class="flex flex-wrap items-center gap-5 text-xs text-neutral-600 mb-4">
    <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
        Tersedia
    </div>
    <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
        Menunggu konfirmasi
    </div>
    <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full bg-neutral-300"></span>
        Sudah terisi
    </div>
</div>

<div class="overflow-x-auto rounded-xl border border-neutral-200">
    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="bg-neutral-900 text-white">
                <th class="py-3 px-4 text-left font-medium w-24">Jam</th>
                @foreach ($days as $day)
                    <th class="py-3 px-4 text-center font-medium">
                        {{ $day }}
                        <span class="block text-[10px] font-normal text-neutral-300">{{ $dates[$day] }}</span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($timeSlots as $time)
                <tr class="border-t border-neutral-100">
                    <td class="py-3 px-4 text-neutral-700 font-medium">{{ $time }}</td>

                    @foreach ($days as $day)
                        @php
                            $status = $schedule[$time][$day] ?? 'available';

                            $styles = match ($status) {
                                'available' => 'bg-emerald-100 text-emerald-700',
                                'pending' => 'bg-amber-100 text-amber-700',
                                'booked' => 'bg-neutral-200 text-neutral-500',
                                default => 'bg-neutral-100 text-neutral-400',
                            };

                            $labelText = match ($status) {
                                'available' => 'Kosong',
                                'pending' => 'Menunggu',
                                'booked' => 'Terisi',
                                default => '-',
                            };
                        @endphp

                        <td class="py-2 px-2 text-center">
                            <span class="block rounded-lg py-2 text-xs font-medium {{ $styles }}">
                                {{ $labelText }}
                            </span>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
