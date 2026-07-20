@foreach ($data as $sks)
    <tr class="bg-white dark:bg-darker dark:border-blue-800 dark:text-gray-400 border-b whitespace-nowrap">
        <td class="p-3">
            {{ $loop->iteration }}
        </td>
        <td class="p-3">
            {{ $sks->nomor_surat }}
        </td>
        <td class="p-3">
            {{ $sks->lama_sakit }} hari
        </td>
        <td class="p-3">
            {{ $sks->tanggal_mulai }}
        </td>
        <td class="p-3">
            {{ $sks->tanggal_akhir }}
        </td>
        <td class="p-3 flex justify-center items-center">
            <a href="{{ route('user.kesehatan.surat-keterangan-sakit.show', $sks->nomor_surat) }}" class="font-medium text-blue-600 dark:text-blue-500">
                <span class="icon-[mdi--show-outline] w-5 h-5"></span>
            </a>
        </td>
    </tr>
@endforeach
