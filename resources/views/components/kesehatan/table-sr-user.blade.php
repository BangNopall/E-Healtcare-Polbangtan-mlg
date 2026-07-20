@foreach ($data as $srs)
    <tr class="bg-white dark:bg-darker dark:border-blue-800 dark:text-gray-400 border-b whitespace-nowrap">
        <td class="p-3">
            {{ $loop->iteration }}
        </td>
        <td class="p-3">
            {{ $srs->nomor_surat }}
        </td>
        <td class="p-3">
            {{ $srs->nama_dokter }}
        </td>
        <td class="p-3">
            {{ $srs->nama_rs }}
        </td>
        <td class="p-3">
            {{ $srs->created_at->format('d F Y') }}
        </td>
        <td class="p-3 flex justify-center items-center">
            <a href="{{ route('user.kesehatan.surat-rujukan.show', $srs->nomor_surat) }}"
                class="font-medium text-blue-600 dark:text-blue-500">
                <span class="icon-[mdi--show-outline] w-5 h-5"></span>
            </a>
        </td>
    </tr>
@endforeach
