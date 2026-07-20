<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informasi Personal') }}
        </h2>
    </header>
    <div class="mt-2 space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="w-full">
                <x-input-label for="nik" :value="'NIK'" />
                <x-input-text id="nik" name="nik" type="number" class="mt-2 block w-full cursor-not-allowed"
                    :value="old('nik', isset($surat) ? $surat->nik : '')" disabled readonly placeholder="350707xxxxxxxxxx" />
            </div>
            <div>
                <x-input-label for="bpjs" :value="'No. BPJS'" />
                <x-input-text id="bpjs" name="bpjs" type="text" class="mt-2 block w-full cursor-not-allowed"
                    :value="old('no_bpjs', isset($surat) ? $surat->no_bpjs : '')" disabled readonly placeholder="000145xxxxxxxx" />
            </div>
            <div>
                <x-input-label for="no_hp" :value="'No. Handphone'" />
                <x-input-text id="no_hp" name="no_hp" type="text" class="mt-2 block w-full cursor-not-allowed"
                    :value="old('no_hp', isset($surat) ? $surat->no_hp : '')" disabled readonly placeholder="081235xxxxxx" />
            </div>
            <div>
                <x-input-label for="tempat_lahir" :value="'Tempat Lahir'" />
                <x-input-text id="tempat_lahir" name="tempat_lahir" type="text"
                    class="mt-2 block w-full cursor-not-allowed" disabled readonly :value="old(
                        'tempat_lahir',
                        isset($surat) ? substr($surat->ttl, 0, strpos($surat->ttl, ',')) : '',
                    )" />
            </div>
            <div>
                <x-input-label for="ttl" :value="'Tanggal Lahir'" />
                <x-input-text id="ttl" name="ttl" type="text" class="mt-2 block w-full cursor-not-allowed"
                    disabled readonly :value="old('ttl', isset($surat) ? substr($surat->ttl, strpos($surat->ttl, ',') + 2) : '')" />

            </div>
            <div>
                <x-input-label for="jenis_kelamin" :value="'Jenis Kelamin'" />
                <x-input-text id="jenis_kelamin" name="jenis_kelamin" type="text"
                    class="mt-2 block w-full cursor-not-allowed" disabled readonly :value="old('jenis_kelamin', isset($surat) ? $surat->jenis_kelamin : '')" />
            </div>
            <div>
                <x-input-label for="golongan_darah" :value="'Golongan Darah'" />
                <x-input-text id="golongan_darah" name="golongan_darah" type="text"
                    class="mt-2 block w-full cursor-not-allowed" disabled readonly placeholder="Golongan Darah"
                    :value="old('golongan_darah', isset($surat) ? $surat->golongan_darah : '')" />
            </div>
            <div>
                <x-input-label for="usia" :value="'Usia'" />
                <x-input-text id="usia" name="usia" type="number" class="mt-2 block w-full cursor-not-allowed"
                    disabled readonly placeholder="Otomatis" :value="old('usia', isset($surat) ? $surat->usia : '')" />
            </div>
        </div>
    </div>
</section>
