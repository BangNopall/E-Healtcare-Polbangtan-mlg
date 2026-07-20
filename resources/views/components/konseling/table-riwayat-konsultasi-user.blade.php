@foreach ($data as $dataPsikolog)
    <tr
        class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
        <td class="px-4 py-2">
            {{ $loop->iteration }}
        </td>
        <td class="px-4 py-2">
            {{ $dataPsikolog->metode_psikologi }}
        </td>
        <td class="px-4 py-2">
            {{ $dataPsikolog->keluhan }}
        </td>
        <td class="px-4 py-2">
            {{ $dataPsikolog->saran }}
        </td>
        <td class="px-4 py-2">
            {{ \Carbon\Carbon::parse($dataPsikolog->tanggal)->isoFormat('dddd, D MMMM Y')}}
        </td>
        <td class="px-4 py-2 text-center">
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('user.konseling.detail-konsultasi', $dataPsikolog->id) }}" class="font-medium text-blue-600 dark:text-blue-500">
                    <span class="icon-[mdi--show-outline] w-5 h-5"></span>
                </a>
            </div>
        </td>
    </tr>
@endforeach
