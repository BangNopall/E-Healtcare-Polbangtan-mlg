@foreach ($data as $key => $request)
    <tr class="bg-white dark:bg-darker dark:border-blue-800 dark:text-gray-400 border-b whitespace-nowrap">
        <td class="p-3">
            {{ $key + 1 }}
        </td>
        <td class="p-3">
            {{ $request->suratRujukan->nomor_surat }}
        </td>
        <td class="p-3">
            {{ $request->suratRujukan->nama_pasien }}
        </td>
        <td class="p-3">
            {{ $request->suratRujukan->nama_dokter }}
        </td>
        <td class="p-3">
            {{ $request->suratRujukan->nama_rs }}
        </td>
        <td class="p-3">
            {{ $request->suratRujukan->created_at->diffForHumans() }}
        </td>
        <td class="p-3 flex justify-center items-center">
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('konseling.detail-surat-rujukan', $request->id) }}" class="font-medium text-blue-600 dark:text-blue-500">
                    <span class="icon-[mdi--show-outline] w-5 h-5"></span>
                </a>
            </div>
        </td>
    </tr>
@endforeach
