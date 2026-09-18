<x-guest-layout>
    <div class="w-full max-w-sm px-4 py-6 space-y-6 bg-white rounded-md dark:bg-darker">
        <h1 class="text-xl font-semibold text-center">Reset Kata Sandi</h1>
        <x-auth.auth-session-status class="" :status="session('status')" />
        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="w-full space-y-1">
                <x-auth.input-auth type="email" name="email" placeholder="Alamat Email" :value="old('email')" autofocus />
                <x-input-error :messages="$errors->get('email')" />
            </div>
            <div class="w-full space-y-1">
                <x-auth.input-auth type="password" name="password" placeholder="Kata Sandi" />
                <x-input-error :messages="$errors->get('password')" />
            </div>
            <div class="w-full space-y-1">
                <x-auth.input-auth type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi" />
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>
            <div>
                <button type="submit"
                    class="w-full px-4 py-2 font-medium text-center text-white transition-colors duration-200 rounded-md bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue focus:ring-offset-1 dark:focus:ring-offset-darker">
                    Reset Kata Sandi
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
