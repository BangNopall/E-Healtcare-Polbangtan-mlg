<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Riwayat Penyakit Dahulu') }}
        </h2>
    </header>
    <form method="post" action="{{ route('profile.create-rpd', $user->id) }}" class="mt-6 space-y-6"
        enctype="multipart/form-data">
        @csrf
        <div class="flex items-center justify-center w-full">
            <label for="dropdoc" id="dropdoc-label"
                class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-darker hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-darkerhover">
                <div id="preview-doc" class="flex items-center justify-center w-full px-3 py-12 hidden">
                    <span class="font-medium" id="docname"></span>
                </div>
                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center" id="doc-content">
                    <span class="icon-[ion--cloud-upload-outline] w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"></span>
                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Tekan untuk
                            unggah</span></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">File .PDF Max 10MB</p>
                </div>
                <input id="dropdoc" name="file_RPD" type="file" class="hidden" accept=".pdf, application/pdf"
                    onchange="previewDoc()" />
            </label>
        </div>

        <div class="flex items-center gap-4">
            <x-secondary-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-secondary-button>
        </div>
    </form>

    <div
        class="border-2 border-gray-300 border-dashed rounded-lg bg-gray-50 dark:hover:bg-gray-800 dark:bg-darker dark:border-gray-600 p-4 mt-6">
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
                <div class="text-gray-800 text-lg dark:text-white">
                    Data Riwayat Penyakit Hidup Tidak Ditemukan
                </div>
            @endif
        @else
            <div class="text-gray-800 text-lg dark:text-white">
                Data Riwayat Penyakit Hidup Tidak Ditemukan
            </div>
        @endisset
    </div>
</section>

<script>
    function previewDoc() {
        const input = document.getElementById("dropdoc");
        const preview = document.getElementById("preview-doc");
        const docContent = document.getElementById("doc-content");
        const docName = document.getElementById("docname");

        if (input.files && input.files.length > 0) {
            const fileNames = Array.from(input.files)
                .map((file) => file.name)
                .join(", ");
            docName.textContent = fileNames;
            preview.classList.remove("hidden");
            docContent.classList.add("hidden");
        } else {
            preview.classList.add("hidden");
            docContent.classList.remove("hidden");
        }
    }
</script>
