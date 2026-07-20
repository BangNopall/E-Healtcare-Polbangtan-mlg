@foreach ($data as $sks)
    <tr class="bg-white dark:bg-darker dark:border-blue-800 dark:text-gray-400 border-b whitespace-nowrap">
        <td class="p-3">
            {{ $loop->iteration }}
        </td>
        <td class="p-3">
            {{ $sks->nomor_surat }}
        </td>
        <td class="p-3">
            {{ $sks->nama_pasien }}
        </td>
        <td class="p-3">
            {{ $sks->jabatan_pasien }}
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
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('kesehatan.form.surat-keterangan-sakit.show', $sks->nomor_surat) }}" class="font-medium text-blue-600 dark:text-blue-500">
                    <span class="icon-[mdi--show-outline] w-5 h-5"></span>
                </a>
                @include('kesehatan.partials.modals.hapus-sks')
                <button type="button" x-on:click.prevent="$dispatch('open-modal', 'hapus-{{ $sks->nomor_surat }}')"
                    class="font-medium text-red-600 dark:text-red-500">
                    <span class="icon-[material-symbols--delete-outline] w-5 h-5"></span>
                </button>
            </div>
        </td>
    </tr>
@endforeach
