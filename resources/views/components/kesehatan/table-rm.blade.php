@foreach ($data as $key => $rekamMedis)
    <tr class="bg-white dark:bg-darker dark:border-blue-800 dark:text-gray-400 border-b whitespace-nowrap">
        <td class="p-3">
            {{ $loop->iteration }}
        </td>
        <td class="p-3">
            <div class="block sm:hidden">
                {{ Str::limit($rekamMedis->keluhan, 15, '...') }}
            </div>
            <div class="hidden sm:block">
                {{ $rekamMedis->keluhan }}
            </div>
        </td>
        <td class="p-3">
            <div class="block sm:hidden">
                {{ Str::limit($rekamMedis->pemeriksaan, 15, '...') }}
            </div>
            <div class="hidden sm:block">
                {{ $rekamMedis->pemeriksaan }}
            </div>
        </td>
        <td class="p-3">
            <div class="block sm:hidden">
                {{ Str::limit($rekamMedis->diagnosa, 15, '...') }}
            </div>
            <div class="hidden sm:block">
                {{ $rekamMedis->diagnosa }}
            </div>
        </td>
        <td class="p-3">
            <div class="block sm:hidden">
                {{ Str::limit($rekamMedis->created_at->diffForHumans(), 15, '...') }}
            </div>
            <div class="hidden sm:block">
                {{ $rekamMedis->created_at->diffForHumans() }}
            </div>
        </td>
        <td class="p-3 flex justify-end">
            <div class="items-center justify-center flex flex-row gap-3">
                <a href="{{ route('kesehatan.detailRekamMedis', $rekamMedis->id) }}" class="font-medium text-blue-600 dark:text-blue-500">
                    <span class="icon-[mdi--show-outline] w-5 h-5"></span>
                </a>
            </div>
        </td>
    </tr>
@endforeach
