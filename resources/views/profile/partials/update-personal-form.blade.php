<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informasi Personal') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Perbaruhi informasi personal Anda untuk melengkapi akun profil.') }}
        </p>
    </header>
    <form method="post" action="" class="mt-6 space-y-6">
        @csrf
        @method('put')
        <div class="w-full">
            <x-input-label for="nik" :value="'NIK'" />
            <x-input-text id="nik" name="nik" type="number" class="mt-2 block w-full" :value="old('nik', $user->nik)" required
                autofocus placeholder="350707xxxxxxxxxx" />
            <x-input-error class="mt-1" :messages="$errors->get('nik')" />
        </div>
        <div>
            <x-input-label for="bpjs" :value="'No. BPJS'" />
            <x-input-text id="bpjs" name="bpjs" type="number" class="mt-2 block w-full" :value="old('bpjs', $user->bpjs)"
                required autofocus placeholder="000145xxxxxxxx" />
            <x-input-error class="mt-1" :messages="$errors->get('bpjs')" />
        </div>
        <div>
            <x-input-label for="hp" :value="'No. Handphone'" />
            <x-input-text id="hp" name="hp" type="number" class="mt-2 block w-full" :value="old('hp', $user->hp)"
                required autofocus placeholder="081235xxxxxx" />
            <x-input-error class="mt-1" :messages="$errors->get('hp')" />
        </div>
        <div>
            <x-input-label for="tanggal_lahir" :value="'Tanggal Lahir'" />
            <x-input-text id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-2 block w-full"
                :value="old('tanggal_lahir', $user->tanggal_lahir)" required autofocus placeholder="000145xxxxxxxx" />
            <x-input-error class="mt-1" :messages="$errors->get('tanggal_lahir')" />
        </div>
        <div>
            <x-input-label for="usia" :value="'Usia'" />
            <x-input-text id="usia" name="usia" type="number" class="mt-2 block w-full cursor-not-allowed"
                :value="old('usia', $user->usia)" placeholder="Otomatis" required autofocus readonly disabled />
            <x-input-error class="mt-1" :messages="$errors->get('usia')" />
        </div>
        <div class="flex items-center gap-4">
            <x-secondary-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-secondary-button>
            @if (session('status') === '')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
