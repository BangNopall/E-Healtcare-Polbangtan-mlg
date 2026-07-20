<x-guest-layout>
    <div class="w-full max-w-sm px-4 py-6 space-y-6 bg-white rounded-md dark:bg-darker">
        <h1 class="text-xl font-semibold text-center">Lupa Kata Sandi?</h1>
        <x-auth-session-status class="" :status="session('status')" />
        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            <div class="text-sm text-center text-gray-800 dark:text-gray-300">Masukan email anda dan kami akan mengirimkan link riset kata sandi melalui email.</div>
            @csrf
            <x-auth.input-auth type="email" name="email" placeholder="Alamat Email" :value="old('email')" autofocus/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div>
                <button type="submit"
                    class="w-full px-4 py-2 font-medium text-center text-white transition-colors duration-200 rounded-md bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue focus:ring-offset-1 dark:focus:ring-offset-darker">
                    Riset Kata Sandi
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
