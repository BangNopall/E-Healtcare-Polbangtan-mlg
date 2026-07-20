<x-guest-layout>
    <div class="w-full max-w-sm px-4 py-6 space-y-6 bg-white rounded-md dark:bg-darker">
        <h1 class="text-xl font-semibold text-center">Register</h1>
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            <div class="w-full space-y-1">
                <x-auth.input-auth type="text" name="name" placeholder="Nama Lengkap" :value="old('name')" autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="w-full space-y-1">
                <x-auth.input-auth type="email" name="email" placeholder="Alamat Email" :value="old('email')" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="w-full space-y-1">
                <x-auth.input-auth type="password" name="password" placeholder="Kata Sandi" :value="old('password')" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="w-full space-y-1">
                <x-auth.input-auth type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi"
                    :value="old('password_confirmation')" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <div>
                <button type="submit"
                    class="w-full px-4 py-2 font-medium text-center text-white transition-colors duration-200 rounded-md bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-1 dark:focus:ring-offset-darker">
                    Register
                </button>
            </div>
        </form>
        <!-- Login link -->
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Sudah memiliki akun? <a href="/login" class="text-blue-600 hover:underline">Login</a>
        </div>
    </div>
</x-guest-layout>
