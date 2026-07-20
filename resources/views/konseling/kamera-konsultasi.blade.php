<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Kamera Konsultasi</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div
            class="bg-white dark:bg-darker rounded-lg text-white drop-shadow-xl w-full sm:w-[95%] md:w-[450px] mx-auto p-3 md:p-5">
            <div class="flex justify-center items-center mb-4 w-auto mx-auto">
                <div id="reader" style="width: 600px"></div>
            </div>
            <div class="flex gap-1 w-full">
                <select id="cameraSelect"
                    class="rounded bg-blue-600 dark:bg-dark border-blue-50 dark:border-gray-900 text-gray-100 w-full focus:ring-0 focus:border-0 block flex-1 text-sm p-2"
                    disabled>
                    <option hidden selected class="bg-blue-600 dark:bg-dark">Pilih Kamera</option>
                </select>
                <button class="rounded bg-blue-600 dark:bg-dark border-blue-50 dark:border-gray-900 text-gray-100 p-2"
                    id="btnstop">Stop Scan</button>
            </div>
            <div class="text-center mb-3 mt-2 space-y-1">
                <div class="text-lg font-semibold text-gray-900 dark:text-white">Kamera Konsultasi</div>
                <p class="text-gray-500 dark:text-gray-300 text-sm text-center">Pindai kode QR untuk konsultasi dengan
                    psikiater.</p>
            </div>
            <div class="bg-gray-100 dark:bg-dark w-full md:w-[400px] mx-auto rounded-lg p-3 space-y-3">
                <h1 class="font-medium text-md text-gray-900 dark:text-white">Profil Anda</h1>
                <div class="border-b border-gray-300 mt-1"></div>
                <div class="flex items-center ml-2">
                    @if ($user->avatar_url == null)
                        <img src="https://placehold.co/36x36" class="rounded-full w-9 h-9 md:w-12 md:h-12"
                            id="fotoProfil" alt="Foto Profil">
                    @else
                        <img src="{{ Storage::url('images/' . $user->avatar_url) }}"
                            class="rounded-full w-9 h-9 md:w-12 md:h-12" id="fotoProfil" alt="Foto Profil">
                    @endif
                    <div class="ml-3">
                        <p class="text-sm text-gray-900 dark:text-gray-200">{{ $user->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('konseling.storeKameraKonsultasi') }}" method="post" id="form">
        @csrf
        <input type="hidden" name="token" id="token">
    </form>
    <script src="{{ asset('src/js/html5-qrcode.min.js') }}"></script>
    <script src="{{ asset('src/js/konseling/kamera.js') }}"></script>
</x-app-layout>
