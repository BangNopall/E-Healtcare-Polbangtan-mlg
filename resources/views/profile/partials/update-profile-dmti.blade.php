<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informasi Personal') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Perbaruhi informasi biodata personal Anda.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update-dmti', $user->id) }}" class="mt-6 space-y-6"
        enctype="multipart/form-data">
        @csrf
        @method('patch')
        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
            <div class="w-full">
                <x-input-label for="nik" :value="'NIK'" />
                <x-input-text id="nik" name="nik" type="number" class="mt-2 block w-full" :value="old('nik', isset($dmti) ? $dmti->nik : '')"
                    required autofocus placeholder="350707xxxxxxxxxx" />
                <x-input-error class="mt-1" :messages="$errors->get('nik')" />
            </div>
            <div>
                <x-input-label for="no_bpjs" :value="'No. BPJS'" />
                <x-input-text id="no_bpjs" name="no_bpjs" type="number" class="mt-2 block w-full" :value="old('no_bpjs', isset($dmti) ? $dmti->no_bpjs : '')"
                    required autofocus placeholder="000145xxxxxxxx" />
                <x-input-error class="mt-1" :messages="$errors->get('no_bpjs')" />
            </div>
            <div>
                <x-input-label for="no_hp" :value="'No. Handphone'" />
                <x-input-text id="no_hp" name="no_hp" type="number" class="mt-2 block w-full" :value="old('no_hp', isset($dmti) ? $dmti->no_hp : '')"
                    required autofocus placeholder="081235xxxxxx" />
                <x-input-error class="mt-1" :messages="$errors->get('no_hp')" />
            </div>
            <div>
                <x-input-label for="tempat_kelahiran" :value="'Tempat Kelahiran'" />
                <x-input-text id="tempat_kelahiran" name="tempat_kelahiran" type="text" class="mt-2 block w-full"
                    :value="old('tempat_kelahiran', isset($dmti) ? $dmti->tempat_kelahiran : '')" required autofocus placeholder="Tempat Kelahiran" />
                <x-input-error class="mt-1" :messages="$errors->get('tempat_kelahiran')" />
            </div>
            <div>
                <x-input-label for="tanggal_lahir" :value="'Tanggal Lahir'" />
                <x-input-text id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-2 block w-full"
                    :value="old('tanggal_lahir', isset($dmti) ? $dmti->tanggal_lahir : '')" required autofocus onchange="hitungUsia(this.value)" />
                <x-input-error class="mt-1" :messages="$errors->get('tanggal_lahir')" />
            </div>
            <div>
                <x-input-label for="jenis_kelamin" :value="'Jenis Kelamin'" />
                <select id="jenis_kelamin" name="jenis_kelamin"
                    class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option hidden selected>Pilih</option>
                    <option value="pria"
                        {{ old('jenis_kelamin', isset($dmti) && $dmti->jenis_kelamin == 'pria' ? 'selected' : '') }}>
                        Pria</option>
                    <option value="wanita"
                        {{ old('jenis_kelamin', isset($dmti) && $dmti->jenis_kelamin == 'wanita' ? 'selected' : '') }}>
                        Wanita
                    </option>
                </select>
                <x-input-error class="mt-1" :messages="$errors->get('jenis_kelamin')" />
            </div>
            <div>
                <x-input-label for="usia" :value="'Usia'" />
                <x-input-text id="usia" name="usia" type="number" class="mt-2 block w-full" :value="old('usia', isset($dmti) ? $dmti->usia : '')"
                    placeholder="Otomatis" required autofocus />
                <x-input-error class="mt-1" :messages="$errors->get('usia')" />
            </div>
            <div>
                <x-input-label for="golongan_darah" :value="'Golongan Darah'" />
                <select id="golongan_darah" name="golongan_darah"
                    class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    {{-- Macam Macam Golongan Darah A+, B+, AB+, O+, A-, B-, AB-, dan O- --}}
                    <option hidden selected>Pilih</option>
                    <option value="A+"
                        {{ old('golongan_darah', isset($dmti) && $dmti->golongan_darah == 'A+' ? 'selected' : '') }}>
                        A+
                    </option>
                    <option value="AB+"
                        {{ old('golongan_darah', isset($dmti) && $dmti->golongan_darah == 'AB+' ? 'selected' : '') }}>
                        AB+
                    </option>
                    <option value="B+"
                        {{ old('golongan_darah', isset($dmti) && $dmti->golongan_darah == 'B+' ? 'selected' : '') }}>
                        B+
                    </option>
                    <option value="O+"
                        {{ old('golongan_darah', isset($dmti) && $dmti->golongan_darah == 'O+' ? 'selected' : '') }}>
                        O+
                    </option>
                    <option value="A-"
                        {{ old('golongan_darah', isset($dmti) && $dmti->golongan_darah == 'A-' ? 'selected' : '') }}>
                        A-
                    </option>
                    <option value="AB-"
                        {{ old('golongan_darah', isset($dmti) && $dmti->golongan_darah == 'AB-' ? 'selected' : '') }}>
                        AB-
                    </option>
                    <option value="B-"
                        {{ old('golongan_darah', isset($dmti) && $dmti->golongan_darah == 'B-' ? 'selected' : '') }}>
                        B-
                    </option>
                    <option value="O-"
                        {{ old('golongan_darah', isset($dmti) && $dmti->golongan_darah == 'O-' ? 'selected' : '') }}>
                        O-
                    </option>
                </select>
                <x-input-error class="mt-1" :messages="$errors->get('golongan_darah')" />
            </div>

        </div>
        <div class="flex items-center gap-4">
            <x-secondary-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-secondary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

<script>
    function hitungUsia(tanggal_lahir) {
        if (!tanggal_lahir) {
            return;
        }

        const today = new Date();
        const birthDate = new Date(tanggal_lahir);
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        document.getElementById('usia').value = age;
    }
</script>
