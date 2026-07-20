<div x-show="activeTab === 'import'"
    class="p-6 bg-white text-medium text-gray-500 dark:text-gray-400 dark:bg-darker rounded-lg w-full">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Import Tab</h3>
    <p class="mb-2 text-sm">
        Anda dapat melakukan impor data barang inventaris dengan unggah file excel yang sesuai dengan format.
    </p>
    <form action="" method="post">
        @csrf
        <div class="flex items-center justify-center w-full">
            <label for="dropzone-file" id="dropzone"
                class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-darker hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-darkerhover">
                <div id="preview-container" class="flex items-center justify-center w-full py-12 hidden">
                    <span class="font-medium" id="filename"></span>
                </div>
                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center" id="label-content">
                    <span class="icon-[ion--cloud-upload-outline] w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"></span>
                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Tekan untuk
                            unggah</span> atau <span class="italic">drag and drop</span></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">CSV, XLS, XLSX</p>
                </div>
                <input id="dropzone-file" type="file" class="hidden"
                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                    onchange="previewFile()" />
            </label>
        </div>
        <x-button class="mt-4 p-2">Import File</x-button>
    </form>
</div>
<x-session-status id="alert-import" class="hidden" color1="bg-red-100 text-red-500 dark:text-red-200 dark:bg-red-500"
    color2="bg-red-200 hover:bg-red-300 dark:bg-red-800 dark:text-red-200 focus:ring-red-400 dark:hover:bg-red-900">
    Format file tidak sesuai. Silahkan unggah file dengan format yang benar.
</x-session-status>
