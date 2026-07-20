<x-app-layout>
    <!-- Page mother -->
    <div class="flex items-center justify-center flex-1 h-full p-4">

        <!-- Page wrapper -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Edit Profile -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            @if ($user->dmti == true)
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-dmti')
                    </div>
                </div>
            @endif

            {{-- CDMI DATA --}}
            @if ($user->cdmi == true)
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-mahasiswa-form')
                    </div>
                </div>
            @endif

            <!-- RPD -->
            @if ($user->role == 'Mahasiswa' || $user->role == 'Karyawan')
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-rpd-form')
                    </div>
                </div>
            @endif
            <!-- Change Password -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('src/js/profile/profile.js') }}"></script>
</x-app-layout>
