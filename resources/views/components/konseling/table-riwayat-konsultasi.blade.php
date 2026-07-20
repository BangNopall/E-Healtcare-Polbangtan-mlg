@foreach ($data as $dataPsikolog)
    <tr
        class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
        <td class="px-4 py-2">
            {{ $loop->iteration }}
        </td>
        <td class="px-4 py-2">
            {{ $dataPsikolog->user->getCDMI->nim }}
        </td>
        <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            {{ $dataPsikolog->user->name }}
        </td>
        <td class="px-4 py-2">
            {{ $dataPsikolog->keluhan }}
        </td>
        <td class="px-4 py-2">
            {{ \Carbon\Carbon::parse($dataPsikolog->tanggal)->isoFormat('dddd, D MMMM Y')}}
        </td>
        <td class="px-4 py-2 text-center">
            @include('konseling.partials.modals.hapus-konsultasi')
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('konseling.detail-konsultasi', $dataPsikolog->id) }}" class="font-medium text-blue-600 dark:text-blue-500">
                    <span class="icon-[mdi--show-outline] w-5 h-5"></span>
                </a>
                <button type="button" x-on:click.prevent="$dispatch('open-modal', 'hapus-{{ $dataPsikolog->id }}');"
                    class="font-medium text-red-600 dark:text-red-500">
                    <span class="icon-[material-symbols--delete-outline] w-6 h-6"></span>
                </button>
            </div>
        </td>
    </tr>
@endforeach
