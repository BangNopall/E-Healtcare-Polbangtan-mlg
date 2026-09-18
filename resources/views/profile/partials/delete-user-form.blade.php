<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ketika akun dihapus, semua data dan sumber daya akun akan hilang secara permanen. Harap perhatikan ulang ketika hapus akun Anda.') }}
        </p>
    </header>
    <x-danger-button x-data="" class="px-4 py-2"
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">{{ __('Hapus Akun') }}</x-danger-button>
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Apakah anda yakin menghapus Akun?') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Sekali lagi ketika akun dihapus, semua sumber daya dan data akan hilang secara permanen. Harap masukan kata sandi untuk konfirmasi apakah anda ingin hapus akun secara permanen.') }}
            </p>
            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-input-text id="password" name="password" type="password" class="mt-2 block w-3/4"
                    placeholder="{{ __('Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button type="button" class="px-4 py-2" x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ms-3 px-4 py-2">
                    {{ __('Hapus Akun') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
