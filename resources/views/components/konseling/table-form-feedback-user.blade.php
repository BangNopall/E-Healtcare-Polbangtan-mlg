@isset($linkTerbaru)
    <tr
        class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
        <td class="px-4 py-2">
            #
        </td>
        <td class="px-4 py-2">
            {{ $jadwal->materi }}
        </td>
        <td class="px-4 py-2">
            {{ $senso->senso->name }}
        </td>
        <td class="px-4 py-2">
            {{ \Carbon\Carbon::parse($jadwal->tanggal)->isoFormat('D MMMM Y') }}
        </td>
        <td class="px-4 py-2 text-center">
            <a href="{{ $linkTerbaru }}" class="font-medium text-blue-600 dark:text-blue-500">
                <span class="icon-[clarity--form-line] w-5 h-5"></span>
            </a>
        </td>
    </tr>
@endisset