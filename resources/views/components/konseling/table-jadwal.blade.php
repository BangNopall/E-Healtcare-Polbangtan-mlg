@foreach ($data as $jadwal)
    <tr
        class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
        <td class="px-4 py-2">
            {{ $loop->iteration }}
        </td>
        <td class="px-4 py-2">
           {{ $jadwal->materi }}
        </td>
        <td class="px-4 py-2">
            {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}
        </td>
        <td class="px-4 py-2 text-center">
            @include('konseling.partials.modals.hapus-jadwal')
            <div class="items-center justify-center flex flex-row gap-3">
                <button type="button" x-on:click.prevent="$dispatch('open-modal', 'hapus-{{ $jadwal->id }}');"
                    class="font-medium text-red-600 dark:text-red-500">
                    <span class="icon-[material-symbols--delete-outline] w-6 h-6"></span>
                </button>
            </div>
        </td>
    </tr>
@endforeach
