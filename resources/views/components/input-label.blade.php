@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-900 dark:text-white']) }}>
    {{ $value ?? $slot }}
</label>
