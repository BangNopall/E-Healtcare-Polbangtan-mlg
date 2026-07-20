@foreach ($data as $skb)
    <tr class="bg-white dark:bg-darker dark:border-blue-800 dark:text-gray-400 border-b whitespace-nowrap">
        <td class="p-3">
            {{ $loop->iteration }}
        </td>
        <td class="p-3">
            {{ $skb->nomor_surat }}
        </td>
        <td class="p-3">
            {{ $skb->nama_pasien }}
        </td>
        <td class="p-3">
            {{ $skb->jabatan_pasien }}
        </td>
        <td class="p-3">
            {{ $skb->diagnosa }}
        </td>
        <td class="p-3">
            {{ $skb->created_at->format('d F Y') }}
        </td>
        <td class="p-3 flex justify-center items-center">
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('kesehatan.form.surat-keterangan-berobat.show', $skb->nomor_surat) }}" class="font-medium text-blue-600 dark:text-blue-500">
                    <span class="icon-[mdi--show-outline] w-5 h-5"></span>
                </a>
                @include('kesehatan.partials.modals.hapus-skb')
                <button type="button" x-on:click.prevent="$dispatch('open-modal', 'hapus-{{ $skb->nomor_surat }}')"
                    class="font-medium text-red-600 dark:text-red-500">
                    <span class="icon-[material-symbols--delete-outline] w-5 h-5"></span>
                </button>
            </div>
        </td>
    </tr>
@endforeach
