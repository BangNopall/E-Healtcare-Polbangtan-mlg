@isset($data)
    @foreach ($data as $feedback)
        <tr
            class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
            <td class="px-4 py-2">
                {{ $loop->iteration }}
            </td>
            <td class="px-4 py-2">
                {{ $feedback->jadwal->materi }}
            </td>
            <td class="px-4 py-2">
                {{ $feedback->senso->name }}
            </td>
            <td class="px-4 py-2">
                {{ $feedback->siswa->name }}
            </td>
            <td class="px-4 py-2">
                {{ \Carbon\Carbon::parse($feedback->jadwal->tanggal)->isoFormat('D MMMM Y') }}
            </td>
            <td class="px-4 py-2 text-center">
                <a href="{{ route('user.konseling.review-feedback-bimbingan', $feedback->id) }}" class="font-medium text-blue-600 dark:text-blue-500">
                <span class="icon-[mdi--show-outline] w-5 h-5"></span>
                </a>
            </td>
        </tr>
    @endforeach
@endisset
