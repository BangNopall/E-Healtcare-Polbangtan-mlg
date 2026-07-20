<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Akun Profil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Perbaruhi informasi akun profil Anda.') }}
        </p>
    </header>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    <form action="{{ route('profile.update-avatar', $user->id) }}" method="post" class="w-full h-full mt-6" enctype="multipart/form-data">
        @csrf
        <div class="relative w-full h-full" id="inputfilefoto">
            @if ($user->avatar_url == null)
                <label for="dropzone-file"
                    class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-md cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-darker hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <span
                            class="icon-[ion--cloud-upload-outline] w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"></span>
                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-semibold text-center">
                            Tekan
                            untuk unggah foto</p>
                    </div>
                </label>
            @else
                <button type="button" id="updateImageButton" class="object-cover flex justify-center w-full p-1 rounded-md">
                    <img src="{{ Storage::url('images/' . $user->avatar_url) }}" alt="Preview"
                        class="object-cover w-[250px] h-[250px] p-1 rounded-md ring-2" />
                </button>
            @endif
            <input id="dropzone-file" type="file" accept="image/*" class="hidden" name="avatar_url"
                onchange="previewFile()" />
        </div>
        <div id="preview-container" class="relative w-[250px] h-[250px] hidden">
            <img id="preview-image" src="#" alt="Preview" class="object-cover mx-auto w-[250px] h-[250px] p-1 rounded-md ring-2 ring-indigo-300 dark:ring-darkerhover" />
            <x-button type="button" onclick="removeImage()"
                class="absolute top-2 p-1.5 rounded-md flex justify-center items-center shadow-md">
                <span class="icon-[la--trash] w-6 h-6"></span>
            </x-button>
            <x-button type="submit"
                class="absolute bottom-2 p-1.5 rounded-md flex justify-center items-center shadow-md">
                <span class="icon-[line-md--uploading-loop] w-6 h-6"></span>
            </x-button>
        </div>
    </form>
    <form method="post" action="{{ route('profile.update') }}" class=" space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="name" :value="'Nama Lengkap'" />
                <x-input-text id="name" name="name" type="text" class="mt-2 block w-full" :value="old('name', $user->name)"
                    required autofocus placeholder="Nama lengkap" />
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>
            <div>
                <x-input-label for="email" :value="'Alamat Email'" />
                <x-input-text id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $user->email)"
                    required autocomplete="username" />
                <x-input-error class="mt-1" :messages="$errors->get('email')" />
            </div>
        </div>
        <div>
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        <div class="flex items-center gap-4">
            <x-secondary-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-secondary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
