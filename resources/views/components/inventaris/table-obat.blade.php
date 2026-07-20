@foreach ($data as $daftarObat)
    <tr
        class="bg-white transition-colors duration-150 border-b dark:bg-[#152D49] dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a3759] whitespace-nowrap">
        <td class="px-4 py-2">
            {{ $loop->iteration }}
        </td>
        <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            <div class="flex items-center">
                @isset($daftarObat->foto_obat)
                    <div class="w-10 h-10 flex-shrink-0 mr-3">
                        <img src="{{ asset('storage/foto_obat/' . $daftarObat->foto_obat) }}"
                            alt="{{ $daftarObat->nama_obat }}" class="w-10 h-10 rounded-lg">
                    </div>
                @endisset
                {{ $daftarObat->nama_obat }}
            </div>
        </td>
        <td class="px-4 py-2">
            {{ $daftarObat->kode_obat }}
        </td>
        <td class="px-4 py-2">
            @isset($daftarObat->satuan_id)
                {{ $daftarObat->SatuanObat->nama_satuan }}
            @endisset
        </td>
        <td class="px-4 py-2">
            {{ $daftarObat->stok }}
        </td>
        <td class="px-4 py-2">
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('inventaris.obat.show', $daftarObat->id) }}"
                    class="font-medium text-blue-600 dark:text-blue-500 mr-2">
                    <span class="icon-[mdi--show-outline] w-6 h-6"></span>
                </a>
                @include('inventaris.partials.modals.hapus')
                <button type="button" x-on:click.prevent="$dispatch('open-modal', 'hapus-item{{ $daftarObat->id }}')"
                    class="font-medium text-red-600 dark:text-red-500">
                    <span class="icon-[material-symbols--delete-outline] w-6 h-6"></span>
                </button>
            </div>
        </td>
    </tr>
@endforeach
