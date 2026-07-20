@props(['headers', 'color'])
<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-{{ $color }} dark:text-gray-400">
        <tr class="whitespace-nowrap">
            @foreach ($headers as $header)
                <th scope="col" class="px-4 py-3">
                    {{ $header }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        {{ $slot }}
    </tbody>
</table>
