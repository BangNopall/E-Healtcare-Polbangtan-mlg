<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informasi Mahasiswa') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Perbaruhi informasi kampus dan asrama Anda untuk melengkapi akun profil.') }}
        </p>
    </header>
    <form method="post" action="{{ route('profile.update-cdmi', $user->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        <div>
            <x-input-label for="nim" :value="'Nomor Induk Mahasiswa'" />
            <x-input-text id="nim" type="number" name="nim" class="mt-2 block w-full" :value="old('nim', isset($cdmi) ? $cdmi->nim : '')" required
                autofocus placeholder="151231xxxxxx" />
            <x-input-error class="mt-1" :messages="$errors->get('nim')" />
        </div>

        @isset($prodis)
            <div>
                <x-input-label for="prodi_id" :value="'Program Studi'" />
                <select id="prodi_id" name="prodi_id"
                    class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option hidden selected>Pilih</option>
                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi->id }}"
                            {{ old('prodi_id', isset($cdmi) ? $cdmi->prodi_id : '') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-1" :messages="$errors->get('prodi_id')" />
            </div>
        @endisset

        @isset($bloks)
            <div>
                <x-input-label for="blok_id" :value="'Blok Gedung'" />
                <select id="blok_id" name="blok_id"
                    class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option hidden selected>Pilih</option>
                    @foreach ($bloks as $blok)
                        <option value="{{ $blok->id }}"
                            {{ old('blok_id', isset($cdmi) ? $cdmi->blok_id : '') == $blok->id ? 'selected' : '' }}>
                            {{ $blok->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-1" :messages="$errors->get('blok_id')" />
            </div>
        @endisset

        <div>
            <x-input-label for="no_ruangan" :value="'Nomor Ruangan'" />
            <x-input-text id="no_ruangan" type="number" name="no_ruangan" class="mt-2 block w-full" :value="old('no_ruangan', isset($cdmi) ? $cdmi->no_ruangan : '')"
                required autofocus placeholder="10" />
            <x-input-error class="mt-1" :messages="$errors->get('no_ruangan')" />
        </div>

        @if($user->senso === 1)
            <div class="mt-3 bg-green-100 dark:bg-green-800 p-3 rounded">
                <p class="text-green-800 dark:text-green-100 text-sm">Anda merupakan Senior Asuh (SENSO)</p>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-secondary-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-secondary-button>
            @if (session('status') === '')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
