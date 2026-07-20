<ol class="flex items-center w-full">
    <li
        class="flex w-full items-center text-blue-600 dark:text-blue-500 after:content-[''] after:w-full after:h-1 after:border-b after:border-blue-100 after:border-4 after:inline-block dark:after:border-blue-800">
        <span
            class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full lg:h-12 lg:w-12 dark:bg-blue-800 shrink-0">
            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600 dark:text-blue-300" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                    d="M4 18V8a1 1 0 0 1 1-1h1.5l1.707-1.707A1 1 0 0 1 8.914 5h6.172a1 1 0 0 1 .707.293L17.5 7H19a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Z" />
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
        </span>
    </li>
    <li
        class="{{ Request::is('medical/form/riwayat-pasien/surat-keterangan-berobat*', 'medical/form/riwayat-pasien/surat-keterangan-sakit*', 'medical/form/riwayat-pasien/surat-keterangan-sehat*', 'medical/form/riwayat-pasien/surat-rujukan*' ) ? 'text-blue-600 dark:text-blue-500 after:border-blue-100 dark:after:border-blue-800' : 'after:border-white dark:after:border-darker' }} flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b  after:border-4 after:inline-block">
        <span
            class="{{ Request::is('medical/form/riwayat-pasien/surat-keterangan-berobat*', 'medical/form/riwayat-pasien/surat-keterangan-sakit*', 'medical/form/riwayat-pasien/surat-keterangan-sehat*', 'medical/form/riwayat-pasien/surat-rujukan*') ? 'bg-blue-100 dark:bg-blue-800' : 'bg-white dark:bg-darker' }} flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0">
            <span
                class="{{ Request::is('medical/form/riwayat-pasien/surat-keterangan-berobat*', 'medical/form/riwayat-pasien/surat-keterangan-sakit*', 'medical/form/riwayat-pasien/surat-keterangan-sehat*', 'medical/form/riwayat-pasien/surat-rujukan*') ? 'text-blue-600 dark:text-blue-300' : 'text-gray-500 dark:text-white' }} icon-[ic--twotone-merge-type] w-5 h-5 lg:w-6 lg:h-6"></span>
        </span>
    </li>
    <li
        class="{{ Request::is('medical/form/riwayat-pasien/surat-keterangan-berobat/hasil*', 'medical/form/riwayat-pasien/surat-keterangan-sakit/hasil*', 'medical/form/riwayat-pasien/surat-keterangan-sehat/hasil*', 'medical/form/riwayat-pasien/surat-rujukan/hasil*') ? 'text-blue-600 dark:text-blue-500 after:border-blue-100 dark:after:border-blue-800' : 'after:border-white dark:after:border-darker' }} flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b  after:border-4 after:inline-block">
        <span
            class="{{ Request::is('medical/form/riwayat-pasien/surat-keterangan-berobat/hasil*', 'medical/form/riwayat-pasien/surat-keterangan-sakit/hasil*', 'medical/form/riwayat-pasien/surat-keterangan-sehat/hasil*', 'medical/form/riwayat-pasien/surat-rujukan/hasil*') ? 'bg-blue-100 dark:bg-blue-800' : 'bg-white dark:bg-darker' }} flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0">
            <span
                class="{{ Request::is('medical/form/riwayat-pasien/surat-keterangan-berobat/hasil*', 'medical/form/riwayat-pasien/surat-keterangan-sakit/hasil*', 'medical/form/riwayat-pasien/surat-keterangan-sehat/hasil*', 'medical/form/riwayat-pasien/surat-rujukan/hasil*') ? 'text-blue-600 dark:text-blue-300' : 'text-gray-500 dark:text-white' }} icon-[quill--paper] w-5 h-5 lg:w-6 lg:h-6"></span>
        </span>
    </li>
    <li class="flex items-center">
        <span
            class="flex items-center justify-center w-10 h-10 bg-white rounded-full lg:h-12 lg:w-12 dark:bg-darker shrink-0">
            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-gray-500 dark:text-white" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M9 2a1 1 0 0 0-1 1H6a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-2a1 1 0 0 0-1-1H9Zm1 2h4v2h1a1 1 0 1 1 0 2H9a1 1 0 0 1 0-2h1V4Zm5.707 8.707a1 1 0 0 0-1.414-1.414L11 14.586l-1.293-1.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4Z"
                    clip-rule="evenodd" />
            </svg>
        </span>
    </li>
</ol>
