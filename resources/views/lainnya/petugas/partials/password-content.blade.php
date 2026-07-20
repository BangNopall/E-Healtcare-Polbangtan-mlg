<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Perbaruhi Kata Sandi') }}
        </h2>
    </header>
    <form method="post" action="{{ route('lainnya.petugas.update-data-password', $user->id) }}" class="mt-6 space-y-4">
        @csrf
        @method('put')
        <div>
            <x-input-label for="update_password_password" :value="'Kata Sandi Baru'" />
            <x-input-text id="update_password_password" name="password" type="password" class="mt-1 block w-full"
                autocomplete="new-password" placeholder="********" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="update_password_password_confirmation" :value="'Konfirmasi Kata Sandi Baru'" />
            <x-input-text id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full" autocomplete="new-password" placeholder="********" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>
        <div class="flex items-center gap-4">
            <x-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
