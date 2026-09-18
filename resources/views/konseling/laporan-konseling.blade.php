<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Export Konseling</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="flex gap-2 justify-center flex-col sm:flex-row">
            <div
                class="bg-white dark:bg-darker rounded-lg drop-shadow-xl w-full sm:w-[95%] md:w-[450px] p-3 md:p-5">
                <div class="text-lg font-medium">Laporan Konsultasi</div>
                <div class="border-b my-2"></div>
                <form action="{{route('konseling.print.laporan-konsultasi')}}" method="post">
                    @csrf
                    <div class="space-y-1">
                        <div class="w-full">
                            <label for="bulan">Pilih bulan :</label>
                        </div>
                        <div class="w-full">
                            <x-text-input type="month" name="bulan" id="bulan"
                                class="text-sm border w-full text-center rounded p-1" value="" required />
                        </div>
                    </div>
                    <x-button type="submit" name="submit" value="pdf" class="p-2 w-full mt-2"></i>
                        Export PDF
                    </x-button>
                    <x-button type="submit" name="submit" value="excel" class="p-2 w-full mt-2"></i>
                        Export Excel
                    </x-button>
                </form>
            </div>
            <div
                class="bg-white dark:bg-darker rounded-lg drop-shadow-xl w-full sm:w-[95%] md:w-[450px] p-3 md:p-5">
                <div class="text-lg font-medium">Laporan Feedback</div>
                <div class="border-b my-2"></div>
                <form action="{{route('konseling.print.laporan-feedback')}}" method="post">
                    @csrf
                    <div class="space-y-1">
                        <div class="w-full">
                            <label for="bulan">Pilih bulan :</label>
                        </div>
                        <div class="w-full">
                            <x-text-input type="month" name="bulan" id="bulan"
                                class="text-sm border w-full text-center rounded p-1" value="" required />
                        </div>
                    </div>
                    <x-button type="submit" name="submit" value="pdf" class="p-2 w-full mt-2"></i>
                        Export PDF
                    </x-button>
                    <x-button type="submit" name="submit" value="excel" class="p-2 w-full mt-2"></i>
                        Export Excel
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
