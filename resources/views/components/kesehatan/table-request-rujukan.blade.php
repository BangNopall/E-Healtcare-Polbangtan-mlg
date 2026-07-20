@foreach ($data as $key => $request)
    <tr
        class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
        <td class="px-4 py-2">
            {{ $key + 1 }}
        </td>
        <td class="px-4 py-2">
            {{ $request->dataPsikolog->user->getCDMI->nim }}
        </td>
        <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            {{ $request->dataPsikolog->user->name }}
        </td>
        <td class="px-4 py-2">
            {{ $request->dataPsikolog->keluhan }}
        </td>
        <td class="px-4 py-2">
            {{ $request->created_at->diffForHumans() }}
        </td>
        <td class="px-4 py-2">
            <?php
            $status = $request->status;
            ?>
            @if ($status == 'submitted')
                Request Baru
            @elseif ($status == 'canceled')
                Request Dibatalkan
            @elseif ($status == 'rejected')
                Request Ditolak
            @elseif ($status == 'completed')
                Request Selesai
            @endif
        </td>
        <td class="px-4 py-2 text-center">
            @include('kesehatan.partials.modals.hapus-request-rujukan')
            @include('kesehatan.partials.modals.alasan-request-rujukan')
            <div class="items-center justify-center flex flex-row gap-3">
                @if ($status == 'submitted')
                    <a href="{{ route('kesehatan.detail-request-rujukan-konseling', ['id' => $request->dataPsikolog->id, 'requestId' => $request->id]) }}"
                        class="font-medium text-blue-600 dark:text-blue-500">
                        <span class="icon-[quill--paper] w-5 h-5"></span>
                    </a>
                    <button type="button" x-on:click.prevent="$dispatch('open-modal', 'hapus-{{ $request->id }}');"
                        class="font-medium text-red-600 dark:text-red-500">
                        <span class="icon-[material-symbols--cancel-outline] w-5 h-5"></span>
                    </button>
                @elseif ($status == 'completed')
                    <a href="{{ route('kesehatan.form.surat-keterangan-rujukan.print', $request->id) }}"
                        class="font-medium text-blue-600 dark:text-blue-500">
                        <span class="icon-[quill--paper] w-5 h-5"></span>
                    </a>
                @elseif ($status == 'rejected' || $status == 'canceled')
                    <button type="button" x-on:click.prevent="$dispatch('open-modal', 'alasan-{{ $request->id }}');"
                        class="font-medium text-blue-600 dark:text-blue-500">
                        <span class="icon-[fluent--text-change-reject-20-regular] w-5 h-5"></span>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@endforeach
