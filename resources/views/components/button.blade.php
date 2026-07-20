<button {{ $attributes->merge(['class' => 'text-center whitespace-nowrap bg-blue-600 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-200 dark:bg-blue-600 text-gray-200 hover:text-gray-500 dark:hover:text-gray-300 rounded-lg text-sm font-medium']) }}>
    {{ $slot }}
</button>