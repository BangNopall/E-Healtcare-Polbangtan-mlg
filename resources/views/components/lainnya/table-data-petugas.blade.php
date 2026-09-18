@foreach ($data as $petugas)
    <tr
        class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
        <td class="px-4 py-2">
            {{ $loop->iteration }}
        </td>
        <th scope="row" class="px-4 py-2 text-gray-900 whitespace-nowrap dark:text-white">
            <div class="font-medium">{{ $petugas->name }}</div>
        </th>
        <td class="px-4 py-2">
            {{ $petugas->email }}
        </td>
        <td class="px-4 py-2">
            {{ $petugas->role }}
        </td>
        <td class="px-4 py-2 text-center">
            @include('lainnya.petugas.partials.modals.hapus')
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('lainnya.petugas.show', $petugas->id) }}"
                    class="font-medium text-blue-600 dark:text-blue-500 mr-2">
                    <span class="icon-[mdi--show-outline] w-6 h-6"></span>
                </a>
                <button type="button" x-on:click.prevent="$dispatch('open-modal', 'hapus-{{ $petugas->id }}');"
                    class="font-medium text-red-600 dark:text-red-500">
                    <span class="icon-[material-symbols--delete-outline] w-6 h-6"></span>
                </button>
            </div>
        </td>
    </tr>
@endforeach
