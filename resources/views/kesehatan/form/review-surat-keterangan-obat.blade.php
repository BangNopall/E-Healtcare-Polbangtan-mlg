<x-app-layout>
    {{-- Content Header --}}
    <div class="flex justify-center px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        @include('kesehatan.partials.stepper')
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="flex flex-col md:flex-row justify-center items-center sm:items-start gap-2 mt-2">
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full md:w-auto">
                <div class="w-full md:max-w-xl">
                    @include('kesehatan.partials.profil-laporan-review')
                </div>
            </div>
            <div class="flex flex-col gap-2 w-full md:w-[930px]">
                <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                    <div class="w-full">
                        @include('kesehatan.partials.info-personal-laporan-review')
                    </div>
                </div>
                @if ($user->cdmi == 1)
                    <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                        <div class="w-full">
                            @include('kesehatan.partials.info-mahasiswa-laporan-review')
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
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-t dark:border-blue-800">
        <div class="flex justify-center flex-col md:flex-row gap-2 max-w-screen-xl mx-auto">
            {{-- MEMBUAT SURAT --}}
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full">
                <div class="w-full">
                    <section>
                        <header>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Surat Keterangan Berobat (PDF)
                            </h2>
                        </header>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="font-medium">
                                Nomor Surat: {{ $surat->nomor_surat }}
                            </div>
                            <div class="text-sm font-medium italic">
                                Yang bertanda tangan di bawah ini:
                            </div>
                            <div class="grid gap-2 md:grid-cols-2 mt-2">
                                <div>
                                    <x-input-label for="nama_dokter" :value="'Nama'" />
                                    <x-input-text id="nama_dokter" name="nama_dokter" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->nama_dokter }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="jabatan_dokter" :value="'Jabatan'" />
                                    <x-input-text id="jabatan_dokter" name="jabatan_dokter" type="text"
                                        class="mt-2 block w-full cursor-not-allowed"
                                        value="{{ $surat->jabatan_dokter }}" disabled readonly />
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="text-sm font-medium italic">
                                Dengan ini menerangkan bahwa:
                            </div>
                            <div class="grid gap-2 md:grid-cols-2 mt-2">
                                <div>
                                    <x-input-label for="nik" :value="'NIK'" />
                                    <x-input-text id="nik" name="nik" type="number"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->nik }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="nama_pasien" :value="'Nama'" />
                                    <x-input-text id="nama_pasien" name="nama_pasien" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->nama_pasien }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="ttl" :value="'Tempat/tgl lahir'" />
                                    <x-input-text id="ttl" name="ttl" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->ttl }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="jenis_kelamin" :value="'Jenis Kelamin'" />
                                    <x-input-text id="jenis_kelamin" name="jenis_kelamin" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->jenis_kelamin }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="usia" :value="'Usia'" />
                                    <x-input-text id="usia" name="usia" type="number"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->usia }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="no_hp" :value="'No. Handphone'" />
                                    <x-input-text id="no_hp" name="no_hp" type="number"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->no_hp }}"
                                        disabled readonly />
                                </div>
                            </div>
                            <div class="mt-3 text-sm">
                                <p>
                                    <?php \Carbon\Carbon::setLocale('id'); ?>
                                    Telah berobat di Klinik Polbangtan Malang pada hari
                                    <span>{{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('l') }}</span>
                                    tanggal <span
                                        id="datenow">{{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y') }}</span>
                                    dengan
                                    diagnosa <span class="uppercase">{{ $surat->diagnosa }}</span>
                                </p>
                                <br>
                                <p>
                                    Demikian surat keterangan ini kami buat dan dapat dipertanggung jawabkan
                                    sebagaimana mestinya.
                                </p>
                            </div>
                        </div>
                        <header>
                            <h2 class="text-xl mt-3 font-semibold text-gray-900 dark:text-gray-100">
                                Pemeriksaan Medis
                            </h2>
                        </header>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div>
                                <x-input-label for="diagnosa" :value="'Diagnosa'" />
                                <x-input-text id="diagnosa" name="diagnosa" type="text"
                                    class="mt-2 block w-full cursor-not-allowed" value="{{ $surat->diagnosa }}"
                                    disabled readonly placeholder="Demam" />
                            </div>
                            <div class="grid gap-2 md:grid-cols-2 mt-2">
                                <div>
                                    <x-input-label for="pemeriksaan" :value="'Pemeriksaan Fisik'" />
                                    <textarea name="pemeriksaan" id="pemeriksaan" disabled readonly cols="7" rows="7"
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $surat->pemeriksaan }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="keluhan" :value="'Keluhan'" />
                                    <textarea name="keluhan" id="keluhan" cols="7" rows="7" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $surat->keluhan }}</textarea>
                                </div>
                            </div>
                        </div>
                        @if ($surat->withObat == 1)
                            <div class="p-3 mt-3 bg-gray-100 dark:bg-dark rounded">
                                <h2 class="text-lg font-medium">Permintaan Obat</h2>
                                <ul class="list-decimal px-3 mt-2 text-sm text-gray-700 dark:text-gray-300">
                                    @foreach ($obatLogs as $obat)
                                        <li>{{ $obat->Obat->nama_obat }} {{ $obat->Qty }}x</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mt-3 bg-green-100 dark:bg-green-800 p-3 rounded">
                            <p class="text-green-800 dark:text-green-100 text-sm">Anda tidak dapat melakukan pembatalan
                                atau perubahan pada surat laporan ini. Anda tetap dapat menghapus surat pada halaman
                                riwayat laporan.</p>
                        </div>
                        <form action="{{ route('kesehatan.form.surat-keterangan-berobat.print', $surat->id) }}"
                            class="flex gap-2 mt-3" method="get">
                            @csrf
                            <x-button class="py-2 px-4"
                                type="submit">{{ __('Cetak Dokumen dan Selesai') }}</x-button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('src/js/kesehatan/surat-laporan.js') }}"></script>
</x-app-layout>
