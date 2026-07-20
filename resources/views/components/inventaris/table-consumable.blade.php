@foreach ($data as $alat)
    <tr
        class="bg-white transition-colors duration-150 border-b dark:bg-[#152D49] dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a3759] whitespace-nowrap">
        <td class="px-4 py-3">
            {{ $loop->iteration }}
        </td>
        <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            <div class="flex items-center">
                @isset($alat->foto_consumable)
                    <div class="w-10 h-10 flex-shrink-0 mr-3">
                        <img src="{{ asset('storage/foto_consumable/' . $alat->foto_consumable) }}"
                            alt="{{ $alat->nama_alat }}" class="w-10 h-10 rounded-lg">
                    </div>
                @endisset
                {{ $alat->nama_alat }}
            </div>
        </td>
        <td class="px-4 py-3">
            {{ $alat->kode_alat }}
        </td>
        <td class="px-4 py-3">
            @isset($alat->kategori_id)
                {{ $alat->KategoriConsumable->nama_kategori }}
            @endisset
        </td>
        <td class="px-4 py-3">
            {{ $alat->stok }}
        </td>
        <td class="px-4 py-3">
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('inventaris.consumable.show', $alat->id) }}"
                    class="font-medium text-blue-600 dark:text-blue-500 mr-2">
                    <span class="icon-[mdi--show-outline] w-6 h-6"></span>
                </a>
                {{-- <form action="{{ route('inventaris.destroyConsumable', $alat->id) }}" method="post">
                    @csrf
                    @method('delete') --}}
                    @include('inventaris.partials.modals.alat-noused.hapus')
                    <button type="button"
                        x-on:click.prevent="$dispatch('open-modal', 'hapus-item{{ $alat->id }}')"
                        class="font-medium text-red-600 dark:text-red-500">
                        <span class="icon-[material-symbols--delete-outline] w-6 h-6"></span>
                    </button>
                {{-- </form> --}}
            </div>
        </td>
    </tr>
@endforeach
