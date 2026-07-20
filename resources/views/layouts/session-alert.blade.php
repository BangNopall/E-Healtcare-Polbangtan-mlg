@if (session('success'))
    <x-session-status color1="text-green-500 bg-green-100 dark:bg-green-600 dark:text-green-100"
        color2="bg-green-200 hover:bg-green-300 dark:bg-green-800 dark:text-green-200 focus:ring-green-400 dark:hover:bg-green-900">
        {{ session('success') }}
    </x-session-status>
@endif
@if (session('error'))
    <x-session-status color1="bg-red-100 text-red-500 dark:text-red-200 dark:bg-red-500"
        color2="bg-red-200 hover:bg-red-300 dark:bg-red-800 dark:text-red-200 focus:ring-red-400 dark:hover:bg-red-900">
        {{ session('error') }}
    </x-session-status>
@endif
