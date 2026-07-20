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
                    :value="old('nik', isset($dmti) && $dmti->user_id == $user->id ? $dmti->nik : '')" disabled readonly placeholder="Kosong" />
            </div>
            <div>
                <x-input-label for="bpjs" :value="'No. BPJS'" />
                <x-input-text id="bpjs" name="bpjs" type="text" class="mt-2 block w-full cursor-not-allowed"
                    :value="old('no_bpjs', isset($dmti) && $dmti->user_id == $user->id ? $dmti->no_bpjs : '')" disabled readonly placeholder="Kosong" />
            </div>
            <div>
                <x-input-label for="hp" :value="'No. Handphone'" />
                <x-input-text id="hp" name="hp" type="text" class="mt-2 block w-full cursor-not-allowed"
                    :value="old('no_hp', isset($dmti) && $dmti->user_id == $user->id ? $dmti->no_hp : '')" disabled readonly placeholder="Kosong" />
            </div>
            <div>
                <x-input-label for="tempat_kelahiran" :value="'Tempat Kelahiran'" />
                <x-input-text id="tempat_kelahiran" name="tempat_kelahiran" type="text"
                    class="mt-2 block w-full cursor-not-allowed" :value="old(
                        'tempat_kelahiran',
                        isset($dmti) && $dmti->user_id == $user->id ? $dmti->tempat_kelahiran : '',
                    )" disabled readonly
                    placeholder="Kosong" />
            </div>
            <div>
                <x-input-label for="tanggal_lahir" :value="'Tanggal Lahir'" />
                <x-input-text id="tanggal_lahir" name="tanggal_lahir" type="date"
                    class="mt-2 block w-full cursor-not-allowed" disabled readonly :value="old(
                        'tanggal_lahir',
                        isset($dmti) && $dmti->user_id == $user->id ? $dmti->tanggal_lahir : '',
                    )" placeholder="Kosong"/>
            </div>
            <div>
                <x-input-label for="jenis_kelamin" :value="'Jenis Kelamin'" />
                <x-input-text id="jenis_kelamin" name="jenis_kelamin" type="text"
                    class="mt-2 block w-full cursor-not-allowed" disabled readonly :value="old(
                        'jenis_kelamin',
                        isset($dmti) && $dmti->user_id == $user->id ? $dmti->jenis_kelamin : '',
                    )" placeholder="Kosong"/>
            </div>
            <div>
                <x-input-label for="golongan_darah" :value="'Golongan Darah'" />
                <x-input-text id="golongan_darah" name="golongan_darah" type="text"
                    class="mt-2 block w-full cursor-not-allowed" disabled readonly :value="old(
                        'golongan_darah',
                        isset($dmti) && $dmti->user_id == $user->id ? $dmti->golongan_darah : '',
                    )"
                    placeholder="Kosong" />
            </div>
            <div>
                <x-input-label for="usia" :value="'Usia'" />
                <x-input-text id="usia" name="usia" type="number" class="mt-2 block w-full cursor-not-allowed"
                    disabled readonly :value="old(
                        'usia',
                        isset($dmti) && $dmti->user_id == $user->id ? $dmti->usia : '',
                    )" placeholder="Kosong" />
            </div>
        </div>
    </div>
</section>
