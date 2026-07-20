<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informasi Mahasiswa') }}
        </h2>
    </header>
    <div class="mt-2 space-y-4">
        <div>
            <x-input-label for="nim" :value="'NIM'" />
            <x-input-text id="nim" name="nim" type="number" class="mt-2 block w-full cursor-not-allowed"
                :value="old('nim', isset($surat) ? $surat->nim : '')" disabled readonly placeholder="151231xxxxxx" />
        </div>
        <div>
            <x-input-label for="prodi" :value="'Program Studi'" />
            <x-input-text id="prodi" name="prodi" type="text" class="mt-2 block w-full cursor-not-allowed"
                :value="old('prodi', isset($surat) ? $surat->prodi->name : '')" disabled readonly placeholder="teknik informatika" />
        </div>
        <div>
            <x-input-label for="blok" :value="'Blok Gedung'" />
            <x-input-text id="blok" name="blok" disabled readonly type="text"
                class="mt-2 block w-full cursor-not-allowed" :value="old('blok', isset($surat) ? $surat->blok->name : '')"  placeholder="B" />
        </div>
        <div>
            <x-input-label for="no_ruangan" :value="'Nomor Ruangan'" />
            <x-input-text id="no_ruangan" disabled readonly name="no_ruangan" type="number"
                class="mt-2 block w-full cursor-not-allowed" :value="old('no_ruangan', isset($surat) ? $surat->no_ruangan : '')" placeholder="10" />
        </div>
    </div>
</section>
