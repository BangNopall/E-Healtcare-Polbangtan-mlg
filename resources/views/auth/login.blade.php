<x-guest-layout>
    <div class="w-full max-w-sm px-4 py-6 space-y-6 bg-white rounded-md dark:bg-darker">
        <h1 class="text-xl font-semibold text-center">Login</h1>
        <x-auth.auth-session-status class="" :status="session('status')" />
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div class="w-full space-y-1">
                <x-auth.input-auth type="email" name="email" placeholder="Alamat Email" :value="old('email')" autofocus />
                <x-input-error :messages="$errors->get('email')" />
            </div>
            <div class="w-full space-y-1">
                <x-auth.input-auth type="password" name="password" placeholder="Kata Sandi" />
                <x-input-error :messages="$errors->get('password')" />
            </div>
            <div class="flex items-center justify-between">
                <!-- Remember me toggle -->
                <label class="flex items-center">
                    <div class="relative inline-flex items-center">
                        <input type="checkbox" name="remembr_me"
                            class="w-10 h-4 transition inicheckbox bg-gray-200 border-none rounded-full shadow-inner outline-none appearance-none toggle checked:bg-blue-400 disabled:bg-gray-200 focus:outline-none" />
                        <span
                            class="absolute top-0 left-0 w-4 h-4 transition-all transform scale-150 bg-white rounded-full shadow-sm"></span>
                    </div>
                    <span class="ml-3 text-sm font-normal text-gray-500 dark:text-gray-400">Ingatkan saya</span>
                </label>

                {{-- <a href="/forgot-password" class="text-sm text-blue-600 hover:underline">Lupa kata sandi?</a> --}}
            </div>
            <div>
                <button type="submit"
                    class="w-full px-4 py-2 font-medium text-center text-white transition-colors duration-200 rounded-md bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue focus:ring-offset-1 dark:focus:ring-offset-darker">
                    Login
                </button>
            </div>
        </form>
        <!-- Register link -->
        {{-- <div class="text-sm text-gray-600 dark:text-gray-400">
            Tidak memiliki akun? <a href="/register" class="text-blue-600 hover:underline">Register</a>
        </div> --}}
    </div>
</x-guest-layout>
