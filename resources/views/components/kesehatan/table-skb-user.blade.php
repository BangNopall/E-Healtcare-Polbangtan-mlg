@foreach ($data as $skb)
<tr class="bg-white dark:bg-darker dark:border-blue-800 dark:text-gray-400 border-b whitespace-nowrap">
    <td class="p-3">
        {{ $loop->iteration }}
    </td>
    <td class="p-3">
        {{ $skb->nomor_surat }}
    </td>
    <td class="p-3">
        {{ $skb->diagnosa }}
    </td>
    <td class="p-3">
        {{ $skb->created_at->format('d F Y') }}
    </td>
    <td class="p-3 flex justify-center items-center">
        <a href="{{ route('user.kesehatan.surat-keterangan-berobat.show', $skb->nomor_surat) }}" class="font-medium text-blue-600 dark:text-blue-500">
            <span class="icon-[mdi--show-outline] w-5 h-5"></span>
        </a>
    </td>
</tr> 
@endforeach

