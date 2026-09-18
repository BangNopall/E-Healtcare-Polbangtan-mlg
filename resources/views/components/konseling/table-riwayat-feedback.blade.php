@isset($data)
    @foreach ($data as $fb)
        <tr
            class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
            <td class="px-4 py-2">
                {{ $loop->iteration }}
            </td>
            <td class="px-4 py-2">
                @if ($fb->siswa->getCDMI)
                    {{ $fb->siswa->nim }}
                @else
                    Data Belum Lengkap
                @endif
            </td>
            <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ $fb->siswa->name }}
            </td>
            <td class="px-4 py-2">
                {{ $fb->jadwal->materi }}
            </td>
            <td class="px-4 py-2">
                {{  // Format tanggal
                    Carbon\Carbon::parse($fb->jadwal->tanggal)->isoFormat('dddd, D MMMM Y')
                }}
            </td>
            <td class="px-4 py-2 text-center">
                @if (!session('sso_readonly'))
                    @include('konseling.partials.modals.hapus-feedback')
                @endif
                <div class="items-center justify-center flex flex-row gap-3">
                    <a href="{{ route('konseling.detail-feedback', $fb->id) }}" class="font-medium text-blue-600 dark:text-blue-500" title="Lihat Detail">
                        <span class="icon-[mdi--show-outline] w-5 h-5"></span>
                    </a>
                    @if (!session('sso_readonly'))
                        <button type="button" x-on:click.prevent="$dispatch('open-modal', 'hapus-{{ $fb->id }}');"
                            class="font-medium text-red-600 dark:text-red-500" title="Hapus Feedback">
                            <span class="icon-[material-symbols--delete-outline] w-6 h-6"></span>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@endisset
