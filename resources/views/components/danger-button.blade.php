<button {{ $attributes->merge(['class' => 'text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 transition-colors duration-300']) }}>
    {{ $slot }}
</button>