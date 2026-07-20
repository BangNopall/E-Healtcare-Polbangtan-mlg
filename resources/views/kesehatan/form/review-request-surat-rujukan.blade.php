<x-app-layout>
    {{-- Content Header --}}
    <div class="flex justify-center px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        @include('kesehatan.partials.stepper')
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div id="accordion-flush" data-accordion="collapse" data-active-classes="bg-transparent"
            data-inactive-classes="text-gray-500 dark:text-gray-400">
            <h2 id="accordion-flush-heading-2">
                <button type="button"
                    class="flex items-center justify-between w-full rounded p-5 font-medium rtl:text-right text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 gap-3"
                    data-accordion-target="#accordion-flush-body-2" aria-expanded="false"
                    aria-controls="accordion-flush-body-2">
                    @if ($user->role == 'Karyawan')
                        <span>Data Karyawan</span>
                    @else
                        <span>Data Mahasiswa</span>
                    @endif
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-flush-body-2" class="hidden" aria-labelledby="accordion-flush-heading-2">
                <div class="flex flex-col md:flex-row justify-center items-center sm:items-start gap-2 mt-2">
                    <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full md:w-auto">
                        <div class="w-full md:max-w-xl">
                            @include('kesehatan.partials.profil-laporan')
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 w-full md:w-[930px]">
                        <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                            <div class="w-full">
                                @include('kesehatan.partials.info-personal-laporan')
                            </div>
                        </div>
                        @if ($user->cdmi == 1)
                            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                                <div class="w-full">
                                    @include('kesehatan.partials.info-mahasiswa-laporan')
                                </div>
                            </div>
                        @endif
                        <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                            <div class="w-full">
                                @include('kesehatan.partials.rpd-laporan')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h2 id="accordion-flush-heading-3">
                <button type="button"
                    class="flex items-center justify-between w-full rounded p-5 font-medium rtl:text-right text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 gap-3"
                    data-accordion-target="#accordion-flush-body-3" aria-expanded="false"
                    aria-controls="accordion-flush-body-3">
                    <span>Hasil Konsultasi</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-flush-body-3" class="hidden" aria-labelledby="accordion-flush-heading-3">
                <div class="flex justify-center flex-col md:flex-row gap-2 max-w-screen-xl mx-auto mt-2">
                    <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full">
                        <div class="w-full">
                            <section>
                                <header>
                                    <div class="flex justify-between">
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                            Konsultasi Konseling
                                        </h2>
                                    </div>
                                </header>
                                <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                    <div class="grid gap-2 md:grid-cols-2">
                                        <div>
                                            <x-input-label for="tanggal" :value="'Tanggal'" />
                                            <x-input-text id="tanggal" name="tanggal" type="date" class="mt-2 block w-full cursor-not-allowed" disabled readonly
                                                value="{{ $dataPsikolog->tanggal }}" />
                                        </div>
                                        <div>
                                            <x-input-label for="metode" :value="'Metode Psikologi'" />
                                            <x-input-text id="metode" name="metode" type="text" class="mt-2 block w-full cursor-not-allowed" disabled readonly
                                                value="{{ $dataPsikolog->metode_psikologi }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                    <div class="grid gap-2 md:grid-cols-2">
                                        <div>
                                            <x-input-label for="keluhan" :value="'Keluhan'" />
                                            <textarea name="keluhan" id="keluhan" cols="5" rows="5" disabled readonly
                                                class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->keluhan }}</textarea>
                                        </div>
                                        <div>
                                            <x-input-label for="diagnosa" :value="'Diagnosa'" />
                                            <textarea name="diagnosa" id="diagnosa" cols="5" rows="5" disabled readonly
                                                class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->diagnosa }}</textarea>
                                        </div>
                                        <div>
                                            <x-input-label for="prognosis" :value="'Prognosis'" />
                                            <textarea name="prognosis" id="prognosis" cols="5" rows="5" disabled readonly
                                                class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->prognosis }}</textarea>
                                        </div>
                                        <div>
                                            <x-input-label for="intervensi" :value="'Intervensi'" />
                                            <textarea name="intervensi" id="intervensi" cols="5" rows="5" disabled readonly
                                                class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->intervensi }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                    <div class="grid gap-2 md:grid-cols-2">
                                        <div>
                                            <x-input-label for="saran" :value="'Saran'" />
                                            <textarea name="saran" id="cek_fisik" cols="5" rows="5" disabled readonly
                                                class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->saran }}</textarea>
                                        </div>
                                        <div>
                                            <x-input-label for="rencana_tindak_lanjut" :value="'Rencana Tindak Lanjut'" />
                                            <textarea name="rencana_tindak_lanjut" id="rencana_tindak_lanjut" cols="5" rows="5" disabled readonly
                                                class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->rencana_tindak_lanjut }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-t dark:border-blue-800">
        <div class="flex justify-center flex-col md:flex-row gap-2 max-w-screen-xl mx-auto">
            {{-- MEMBUAT SURAT --}}
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full">
                <div class="w-full">
                    <section>
                        <header>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Surat Rujukan (PDF)
                            </h2>
                        </header>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="font-medium">
                                Nomor Surat: {{ $surat->nomor_surat }}
                            </div>
                            <div class="text-sm font-medium italic">
                                Merujuk kepada:
                            </div>
                            <div class="grid gap-2 md:grid-cols-2 mt-2">
                                <div>
                                    <x-input-label for="nama" :value="'Nama'" />
                                    <x-input-text id="nama" name="nama" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->nama_dokter }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="rs" :value="'Rumah Sakit / Puskesmas'" />
                                    <x-input-text id="rs" name="rs" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->nama_rs }}"
                                        disabled readonly />
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="text-sm font-medium italic">
                                Mohon pemeriksaan dan pengobatan lebih lanjut terhadap penderita:
                            </div>
                            <div class="grid gap-2 md:grid-cols-2 mt-2">
                                <div>
                                    <x-input-label for="nik" :value="'NIK'" />
                                    <x-input-text id="nik" name="nik" type="number"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->nik }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="user" :value="'Nama'" />
                                    <x-input-text id="user" name="user" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->nama_pasien }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="lahir" :value="'Tempat/tgl lahir'" />
                                    <x-input-text id="lahir" name="lahir" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->ttl }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="kelamin" :value="'Jenis Kelamin'" />
                                    <x-input-text id="kelamin" name="kelamin" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" disabled readonly
                                        value="{{ $surat->jenis_kelamin }}" />
                                </div>
                                <div>
                                    <x-input-label for="umur" :value="'Umur'" />
                                    <x-input-text id="umur" name="umur" type="number"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->usia }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="nohp" :value="'No. Handphone'" />
                                    <x-input-text id="nohp" name="nohp" type="number"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->no_hp }}"
                                        disabled readonly />
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="text-sm font-medium italic">
                                Anamnese:
                            </div>
                            <div class="grid gap-2 md:grid-cols-2 mt-2">
                                <div>
                                    <x-input-label for="keluhan" :value="'Keluhan'" />
                                    <textarea name="keluhan" id="cek_fisik" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $surat->keluhan }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="diagnosa" :value="'Diagnosa Sementara'" />
                                    <textarea name="diagnosa" id="diagnosa" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $surat->diagnosa }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="kasus" :value="'Kasus'" />
                                    <textarea name="kasus" id="kasus" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $surat->kasus }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="teroba" :value="'Terapi/Obat yang telah diberikan'" />
                                    <textarea name="teroba" id="teroba" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $surat->tindakan }}</textarea>
                                </div>
                            </div>
                            <div class="mt-3 text-sm">
                                <p>
                                    Demikian surat rujukan ini kami kirim, kami mohon balasan atas surat rujukan
                                    ini. Atas perhatian Bapak/Ibu kami ucapkan terima kasih.
                                </p>
                            </div>
                        </div>
                        <div class="mt-3 bg-green-100 dark:bg-green-800 p-3 rounded">
                            <p class="text-green-800 dark:text-green-100 text-sm">Anda tidak dapat melakukan pembatalan
                                atau perubahan pada surat laporan ini. Anda tetap dapat menghapus surat pada halaman
                                riwayat laporan.</p>
                        </div>
                        <form action="{{ route('kesehatan.form.surat-rujukan.print', $surat->id) }}"
                            class="flex gap-2 mt-3" method="get">
                            @csrf
                            <x-button class="py-2 px-4"
                                type="submit">{{ __('Cetak Dokumen') }}</x-button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('src/js/kesehatan/surat-laporan.js') }}"></script>
</x-app-layout>
