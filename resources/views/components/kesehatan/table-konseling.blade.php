@foreach ($data as $item)
    <tr class="bg-white dark:bg-darker dark:border-blue-800 dark:text-gray-400 border-b whitespace-nowrap">
        <td class="p-3">
            {{ $loop->iteration }}
        </td>
        <td class="p-3">
            {{ $item->metode_psikologi }}
        </td>
        <td class="p-3">
            {{ $item->diagnosa }}
        </td>
        <td class="p-3">
            {{ $item->diagnosa }}
        </td>
        <td class="p-3">
            {{ $item->prognosis }}
        </td>
        <td class="p-3">
            {{ $item->created_at->format('d F Y') }}
        </td>
        <td class="p-3 align-middle">
            <a href="/konseling/riwayat-konsultasi/{{ $item->id }}" class="font-medium text-blue-600 dark:text-blue-500">
                <span class="icon-[mdi--show-outline] w-5 h-5"></span>
            </a>
        </td>
    </tr>
@endforeach
