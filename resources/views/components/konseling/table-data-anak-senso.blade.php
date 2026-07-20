@foreach ($data as $anak)
    <tr
        class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
        <td class="px-4 py-2">
            {{ $loop->iteration }}
        </td>
        <td class="px-4 py-2">
            @if ($anak->siswa->cdmi_complete === 1)
                {{ $anak->siswa->getCDMI->nim }}
            @else
                Data tidak ditemukan
            @endif
        </td>
        <th scope="row" class="px-4 py-2 text-gray-900 whitespace-nowrap dark:text-white">
            <div class="font-medium">{{ $anak->siswa->name }}</div>
        </th>
        <td class="px-4 py-2">
            {{ $anak->siswa->email }}
        </td>
        <td class="px-4 py-2">
            @if ($anak->siswa->cdmi_complete === 1)
            {{ $anak->siswa->getCDMI->prodi->name }}
            @else
                Data tidak ditemukan
            @endif
        </td>
        <td class="px-4 py-2 text-center">
            @include('konseling.partials.modals.hapus-anak-senso')
            <div class="items-center justify-center flex flex-row gap-3">
                <button type="button" x-on:click.prevent="$dispatch('open-modal', 'hapus-{{ $anak->siswa->id }}');"
                    class="font-medium text-red-600 dark:text-red-500">
                    <span class="icon-[material-symbols--delete-outline] w-6 h-6"></span>
                </button>
            </div>
        </td>
    </tr>
@endforeach
