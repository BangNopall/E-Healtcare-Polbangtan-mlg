<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Riwayat Penyakit Dahulu') }}
        </h2>
    </header>
    <div
        class="border-2 border-gray-300 border-dashed rounded-lg  bg-gray-50 dark:hover:bg-bray-800 dark:bg-darker dark:border-gray-600 p-4 mt-6">
        @isset($user->RPD)
            @if ($user->RPD->count() > 0)
                <ul class="list-decimal px-4">
                    @foreach ($user->RPD as $document)
                        <li>
                            <a href="{{ Storage::url('RPD/' . $document->file_name) }}" target="_blank"
                                class="underline text-blue-500">{{ $document->file_name }}</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-gray-800 text-sm dark:text-white">
                    Data Riwayat Penyakit Hidup Tidak Ditemukan
                </div>
            @endif
        @else
            <div class="text-gray-800 text-sm dark:text-white">
                Data Riwayat Penyakit Hidup Tidak Ditemukan
            </div>
        @endisset
    </div>
</section>
