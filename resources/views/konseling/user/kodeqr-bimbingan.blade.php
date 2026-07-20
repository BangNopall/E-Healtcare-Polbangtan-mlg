<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Kode QR Bimbingan</h1>
    </div>
    {{-- Main Content --}}
    @include('konseling.partials.modals.qr-bimbingan')
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="bg-white dark:bg-darker rounded-lg shadow-lg w-full md:max-w-xl mx-auto p-3 md:p-5">
            <div class="relative max-w-sm transition-all duration-300 filter mx-auto w-[60%] mt-5">
                <img class="rounded-lg w-full bg-gray-500 dark:bg-dark" src="{{ asset('img/qr-test-aja.svg') }}"
                    alt="Kode QR">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg"></div>
                    <div class="relative z-10">
                        <x-button class="py-2 px-4 text-lg md:text-xl" type="button"
                            x-on:click.prevent="$dispatch('open-modal', 'qr');">Buka</x-button>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <div class="text-xl font-medium">Kode QR</div>
                <div class="text-sm text-gray-700 dark:text-gray-200">
                    Pindai kode QR untuk menjalani prosedur bimbingan konseling
                </div>
            </div>
            <div class="bg-gray-100 dark:bg-dark w-full rounded-lg mt-5 p-3">
                <h1 class="font-semibold text-md text-black dark:text-white">Profil Anda</h1>
                <div class="border-b border-gray-400 dark:border-gray-300 mt-1 mb-3"></div>
                <div class="flex items-center mb-2">
                    <div class="w-9 h-9 md:w-12 md:h-12 rounded-full">
                        @if ($user->avatar_url == null)
                            <img src="https://placehold.co/36x36" class="rounded-full w-full h-full"
                                id="fotoProfil" alt="Foto Profil">
                        @else
                            <img src="{{ Storage::url('images/' . $user->avatar_url) }}"
                                class="rounded-full w-full h-full" id="fotoProfil" alt="Foto Profil">
                        @endif
                    </div>
                    <div class="ml-2">
                        <p class="text-sm text-black font-medium dark:text-gray-200">{{ $user->name }}</p>
                    </div>
                </div>
                <ul class="text-gray-900 dark:text-gray-200 text-sm">
                    <li>
                        <span class="font-medium text-black dark:text-white">Email : </span>{{ $user->email }}
                    </li>
                    <li>
                        <span class="font-medium text-black dark:text-white">No. HP : </span>
                        {{ $user->getDMTI->no_hp }}
                    </li>
                    <li>
                        <span class="font-medium text-black dark:text-white">Usia : </span> {{ $user->getDMTI->usia }}
                    </li>
                    {{-- <li>
                        <span class="font-medium text-black dark:text-white">Token : </span> {{ $user->bimbingan_token }}
                    </li>
                    <li>
                        <span class="font-medium text-black dark:text-white">Token Expired: </span> {{ $user->bimbingan_token_expired_at }}
                    </li> --}}
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
