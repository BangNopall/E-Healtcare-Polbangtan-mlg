<button {{ $attributes->merge(['class' => 'text-center whitespace-nowrap transition-colors duration-200 rounded-lg text-sm font-medium hover:text-gray-900 bg-white hover:bg-gray-200 dark:bg-darker dark:hover:bg-darkerhover dark:hover:text-white']) }}>
    {{ $slot }}
</button>