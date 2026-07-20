@foreach ($presensi as $pr)
    <tr
        class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
        <td class="px-4 py-2">
            {{ $loop->iteration }}
        </td>
        <td class="px-4 py-2">
            {{ $pr->jadwal->materi }}
        </td>
        <td class="px-4 py-2">
            {{ $pr->senso->name }}
        </td>
        <td class="px-4 py-2">
            @if ($pr->status == 'Hadir')
                <span
                    class="px-2 py-1 text-xs font-semibold leading-tight rounded-full bg-green-100 text-green-500 dark:bg-green-500 dark:text-white">
                    Hadir
                </span>
            @endif
            @if ($pr->status == 'Sakit')
                <span
                    class="px-2 py-1 text-xs font-semibold leading-tight rounded-full bg-yellow-100 text-yellow-500 dark:bg-yellow-500 dark:text-white">
                    Sakit
                </span>
            @endif
            @if ($pr->status == 'Izin')
                <span
                    class="px-2 py-1 text-xs font-semibold leading-tight rounded-full bg-orange-100 text-orange-500 dark:bg-orange-500 dark:text-white">
                    Izin
                </span>
            @endif
            @if ($pr->status == 'Alpha')
                <span
                    class="px-2 py-1 text-xs font-semibold leading-tight rounded-full bg-red-100 text-red-500 dark:bg-red-500 dark:text-white">
                    Alpha
                </span>
            @endif
        </td>
        <td class="px-4 py-2">
            {{ $pr->jadwal->tanggal }}
        </td>
        <td class="px-4 py-2">
            {{ $pr->jam_presensi }}
        </td>
    </tr>
@endforeach
