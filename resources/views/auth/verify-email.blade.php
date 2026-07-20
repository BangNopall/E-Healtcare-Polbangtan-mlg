<x-guest-layout>
    <div class="w-full max-w-lg px-4 py-6 space-y-6 bg-white rounded-md dark:bg-darker">
        <h1 class="text-xl font-semibold text-center">Verifikasi Email</h1>
        @if (session('status') == 'verification-link-sent')
            <div class="text-center font-medium text-sm text-green-600 dark:text-green-400">
                {{ __('Verifikasi link baru telah di kirim, harap cek kembali email Anda.') }}
            </div>
        @endif
        <form method="POST" action="{{ route('verification.send') }}" class="space-y-6">
            <div class="text-sm text-center text-gray-800 dark:text-gray-300">Terima kasih telah mendaftar! Silahkan buka email untuk
                melakukan verifikasi dengan link yang telah dikirim. Apabila tidak menerima harap tekan tombol
                dibawah untuk menerima email baru.</div>
            @csrf
            <div>
                <button type="submit"
                    class="w-full px-4 py-2 font-medium text-center text-white transition-colors duration-200 rounded-md bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue focus:ring-offset-1 dark:focus:ring-offset-darker">
                    Kirim Ulang
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
