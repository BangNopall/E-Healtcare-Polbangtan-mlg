<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Form Feedback</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        @if (isset($feedbackStatus) && $feedbackStatus === 'belum_ada_senso')
            <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-dark dark:text-yellow-300 border border-yellow-200 dark:border-yellow-700" role="alert">
                <span class="font-medium">Perhatian:</span> Anda belum dipasangkan dengan pembimbing (Senso). Silakan hubungi admin atau psikolog untuk penugasan pembimbing asuh Anda.
            </div>
        @elseif (isset($feedbackStatus) && $feedbackStatus === 'tidak_ada_jadwal')
            <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-dark dark:text-blue-300 border border-blue-200 dark:border-blue-700" role="alert">
                <span class="font-medium">Informasi:</span> Tidak ada jadwal bimbingan untuk hari ini.
            </div>
        @elseif (isset($feedbackStatus) && $feedbackStatus === 'belum_presensi')
            <div class="p-4 mb-4 text-sm text-amber-800 rounded-lg bg-amber-50 dark:bg-dark dark:text-amber-300 border border-amber-200 dark:border-amber-700" role="alert">
                <span class="font-medium">Menunggu Presensi:</span> Pembimbing Anda ({{ optional(optional($senso)->senso)->name ?? 'Senso' }}) belum melakukan presensi untuk jadwal bimbingan hari ini. Form feedback akan otomatis aktif setelah pembimbing melakukan pemindaian QR presensi.
            </div>
        @elseif (isset($feedbackStatus) && $feedbackStatus === 'sudah_isi')
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-dark dark:text-green-300 border border-green-200 dark:border-green-700" role="alert">
                <span class="font-medium">Terima Kasih:</span> Anda telah mengisi feedback untuk sesi bimbingan hari ini. Ulasan Anda dapat dilihat pada tabel riwayat di bawah.
            </div>
        @endif

        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-darker dark:text-gray-400">
                    <tr class="whitespace-nowrap">
                        <th scope="col" class="px-4 py-3">
                            #
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Judul Materi
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Pembimbing
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Tanggal
                        </th>
                        <th scope="col" class="px-4 py-3 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody id="result">
                    @isset($linkFeedbackTerbaru)
                        <x-konseling.table-form-feedback-user :linkTerbaru="$linkFeedbackTerbaru"
                            :senso="$senso" :jadwal="$jadwal" />
                    @else
                        <tr class="bg-white border-b dark:bg-dark dark:border-gray-700">
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                @if (isset($feedbackStatus) && $feedbackStatus === 'sudah_isi')
                                    <span class="text-green-600 dark:text-green-400 font-medium">Feedback hari ini telah selesai diisi.</span>
                                @elseif (isset($feedbackStatus) && $feedbackStatus === 'belum_presensi')
                                    <span>Menunggu pembimbing melakukan presensi...</span>
                                @elseif (isset($feedbackStatus) && $feedbackStatus === 'belum_ada_senso')
                                    <span>Belum ada pembimbing yang ditugaskan.</span>
                                @else
                                    <span>Tidak ada form feedback bimbingan yang aktif saat ini.</span>
                                @endif
                            </td>
                        </tr>
                    @endisset
                </tbody>
            </table>
        </div>
    </div>

    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">History Feedback</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-darker dark:text-gray-400">
                    <tr class="whitespace-nowrap">
                        <th scope="col" class="px-4 py-3">
                            #
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Judul Materi
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Pembimbing
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Siswa
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Tanggal
                        </th>
                        <th scope="col" class="px-4 py-3 text-center">

                        </th>
                    </tr>
                </thead>
                <tbody id="result">
                    @isset($feedback)
                        <x-konseling.table-form-review-feedback-user :data="$feedback" :jadwal="$jadwal"
                            :senso="$senso" />
                    @endisset
                </tbody>
            </table>
        </div>
        {{-- pagination --}}
        <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between mt-2"
            aria-label="Table navigation">
            <span
                class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                <span class="font-semibold text-gray-900 dark:text-white">{{ $feedback->firstItem() }} -
                    {{ $feedback->lastItem() }}</span> of <span
                    class="font-semibold text-gray-900 dark:text-white">{{ $feedback->total() }}</span></span>
            <ul class="inline-flex -space-x-px text-sm h-8">
                <li>
                    @if ($feedback->onFirstPage())
                        <span
                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">
                            <del>
                                Previous
                            </del>
                        </span>
                    @else
                        <a href="{{ $feedback->previousPageUrl() }}"
                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">Previous</a>
                    @endif
                </li>
                @foreach ($feedback->getUrlRange($feedback->currentPage() - 1, $feedback->currentPage() + 1) as $num => $url)
                    <li>
                        <a href="{{ $url }}"
                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 {{ $num == $feedback->currentPage() ? 'text-blue-600 bg-blue-50 dark:bg-[#1a304a] dark:border-gray-700 dark:text-white dark:hover:bg-[#1a304a] dark:hover:text-white' : 'hover:text-blue-700 hover:bg-blue-50 border-gray-300 bg-white text-gray-500 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white' }}">{{ $num }}</a>
                    </li>
                @endforeach
                <li>
                    @if ($feedback->hasMorePages())
                        <a href="{{ $feedback->nextPageUrl() }}"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">Next</a>
                    @else
                        <span
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">
                            <del>
                                Next
                            </del>
                        </span>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
</x-app-layout>
