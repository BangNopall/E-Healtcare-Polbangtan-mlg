<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Akun Profil') }}
        </h2>
    </header>
    <form action="{{ route('lainnya.petugas.update-foto', $user->id) }}" method="post" class="w-full h-full my-3"
        enctype="multipart/form-data">
        @csrf
        <div class="relative w-full h-full" id="inputfilefoto">
            @if ($user->avatar_url == null)
                <label for="dropzone-file"
                    class="flex flex-col items-center justify-center w-full h-full md:w-[250px] border-2 border-gray-300 border-dashed rounded-md cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-darker hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <span
                            class="icon-[ion--cloud-upload-outline] w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"></span>
                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-semibold text-center">
                            Tekan untuk unggah foto</p>
                    </div>
                </label>
            @else
                <button type="button" id="updateImageButton"
                    class="object-cover flex justify-center w-full md:w-[250px] p-1 rounded-md">
                    <img src="{{ Storage::url('images/' . $user->avatar_url) }}" alt="Preview"
                        class="object-cover w-[200px] p-1 rounded-md ring-2 ring-indigo-300 dark:ring-darkerhover" />
                </button>
            @endif
            <input id="dropzone-file" type="file" accept="image/*" class="hidden" name="avatar_url"
                onchange="previewFile()" />
        </div>
        <div id="preview-container" class="relative w-full h-full hidden">
            <img id="preview-image" src="#" alt="Preview"
                class="object-cover w-[250px] h-[250px] p-1 rounded-md ring-2 ring-indigo-300 dark:ring-darkerhover" />
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

    <form action="{{ route('lainnya.petugas.hapus-foto', $user->id) }}" method="post">
        @csrf
        <x-danger-button class="py-2 px-4" type="submit">{{ __('Hapus Foto') }}</x-danger-button>
    </form>

    <form method="post" action="{{ route('lainnya.petugas.update-data-petugas', $user->id) }}"
        class="mt-6 space-y-4" enctype="multipart/form-data">
        @csrf
        @method('patch')
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-input-label for="name" :value="'Nama Lengkap'" />
                <x-input-text id="name" name="name" type="text" class="mt-2 block w-full" :value="old('name', isset($user) ? $user->name : '')"
                    required placeholder="Nama lengkap" />
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="email" :value="'Alamat Email'" />
                <x-input-text id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', isset($user) ? $user->email : '')"
                    required autocomplete="email" />
                <x-input-error class="mt-1" :messages="$errors->get('email')" />
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="role" :value="'Role'" />
                <select name="role" id="role"
                    class="block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 w-full dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="Admin" {{ old('role', isset($user) && $user->role == 'Admin' ? 'selected' : '') }}>
                        Admin
                    </option>
                    <option value="Psikolog"
                        {{ old('role', isset($user) && $user->role == 'Psikolog' ? 'selected' : '') }}>
                        Psikolog
                    </option>
                    <option value="Mahasiswa"
                        {{ old('role', isset($user) && $user->role == 'Mahasiswa' ? 'selected' : '') }}>
                        Mahasiswa
                    </option>
                    <option value="Karyawan"
                        {{ old('role', isset($user) && $user->role == 'Karyawan' ? 'selected' : '') }}>
                        Karyawan
                    </option>
                </select>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <x-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
