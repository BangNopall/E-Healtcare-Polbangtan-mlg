@foreach ($data as $key => $item)
    <tr
        class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
        <td class="px-4 py-2">
            {{ ($data->currentPage() - 1) * $data->perPage() + $key + 1 }}
        </td>
        <th scope="row" class="px-4 py-2 text-gray-900 whitespace-nowrap dark:text-white">
            <div class="font-medium">{{ $item->name }}</div>
        </th>
        <td class="px-4 py-2">
            {{ $item->email }}
        </td>
        <td class="px-4 py-2">
            {{ $item->role }}
        </td>
        <td class="px-4 py-2">
            {{ $item->PasienRM->count() }}
        </td>
        <td class="px-4 py-2 text-center">
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('kesehatan.riwayat-pasien', $item->id) }}" class="font-medium text-blue-600 dark:text-blue-500-500 mr-2">
                    <span class="icon-[quill--paper] w-6 h-6"></span>
                </a>
            </div>
        </td>
    </tr>
@endforeach
