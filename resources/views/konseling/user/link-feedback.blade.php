<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Form Feedback</h1>
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
                            Tanggal
                        </th>
                        <th scope="col" class="px-4 py-3 text-center">

                        </th>
                    </tr>
                </thead>
                <tbody id="result">
                    @isset($linkFeedbackTerbaru)
                        <x-konseling.table-form-review-feedback-user :linkTerbaru="$linkFeedbackTerbaru"
                            :senso="$senso" />
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
