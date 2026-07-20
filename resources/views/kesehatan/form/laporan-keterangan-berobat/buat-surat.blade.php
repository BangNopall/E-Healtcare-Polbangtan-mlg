<x-app-layout>
    {{-- Content Header --}}
    <div class="flex justify-center px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        @include('kesehatan.partials.stepper')
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="flex flex-col md:flex-row justify-center items-center sm:items-start gap-2">
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
                {{-- @if ($user->cdmi == 1) --}}
                <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                    <div class="w-full">
                        @include('kesehatan.partials.info-mahasiswa-laporan')
                    </div>
                </div>
                {{-- @endif --}}
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
                        <form action="{{ route('kesehatan.form.store.skb', $rm->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                <div class="text-sm font-medium italic">
                                    Yang bertanda tangan di bawah ini:
                                </div>
                                <div class="grid gap-2 md:grid-cols-2 mt-2">
                                    <div>
                                        <x-input-label for="nama_dokter" :value="'Nama'" />
                                        <x-input-text id="nama_dokter" name="nama_dokter" type="text"
                                            class="mt-2 block w-full" :value="Auth::user()->name" required
                                            placeholder="dr. Example" />
                                        <x-input-error class="mt-1" :messages="$errors->get('nama_dokter')" />
                                    </div>
                                    <div>
                                        <x-input-label for="jabatan_dokter" :value="'Jabatan'" />
                                        <select id="jabatan_dokter" name="jabatan_dokter"
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option selected value="Dokter">Dokter</option>
                                            <option value="Psikolog">Psikolog</option>
                                            <option value="Perawat">Perawat</option>
                                        </select>
                                        <x-input-error class="mt-1" :messages="$errors->get('jabatan_dokter')" />
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
                                            class="mt-2 block w-full" :value="old(
                                                'nik',
                                                isset($dmti) && $dmti->user_id == $user->id ? $dmti->nik : '',
                                            )" required
                                            placeholder="1231231231231231231" />
                                        <x-input-error class="mt-1" :messages="$errors->get('nik')" />
                                    </div>
                                    <div>
                                        <x-input-label for="nama_pasien" :value="'Nama'" />
                                        <x-input-text id="nama_pasien" name="nama_pasien" type="text"
                                            class="mt-2 block w-full" :value="old('name', isset($user) ? $user->name : '')" required placeholder="Alex" />
                                        <x-input-error class="mt-1" :messages="$errors->get('nama_pasien')" />
                                    </div>

                                    @php
                                        $tanggalLahirFormatted =
                                            isset($dmti) && $dmti->user_id == $user->id
                                                ? \Carbon\Carbon::parse($dmti->tanggal_lahir)->translatedFormat('d F Y')
                                                : '';
                                        $tempatKelahiran =
                                            isset($dmti) && $dmti->user_id == $user->id ? $dmti->tempat_kelahiran : '';
                                    @endphp

                                    <div>
                                        <x-input-label for="ttl" :value="'Tempat/tgl lahir'" />
                                        <x-input-text id="ttl" name="ttl" type="text"
                                            class="mt-2 block w-full" :value="old(
                                                'tempat_kelahiran',
                                                $tempatKelahiran .
                                                    ($tempatKelahiran && $tanggalLahirFormatted ? ', ' : '') .
                                                    $tanggalLahirFormatted,
                                            )" required
                                            placeholder="Malang, 10 Agustus 1945" />
                                        <x-input-error class="mt-1" :messages="$errors->get('ttl')" />
                                    </div>

                                    <div>
                                        <x-input-label for="jenis_kelamin" :value="'Jenis Kelamin'" />
                                        <select id="jenis_kelamin" name="jenis_kelamin"
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="pria"
                                                {{ old('jenis_kelamin', isset($dmti) && $dmti->user_id == $user->id && $dmti->jenis_kelamin == 'pria' ? 'selected' : '') }}>
                                                Pria
                                            </option>
                                            <option value="wanita"
                                                {{ old('jenis_kelamin', isset($dmti) && $dmti->user_id == $user->id && $dmti->jenis_kelamin == 'wanita' ? 'selected' : '') }}>
                                                Wanita
                                            </option>
                                        </select>
                                        <x-input-error class="mt-1" :messages="$errors->get('jenis_kelamin')" />
                                    </div>
                                    <div>
                                        <x-input-label for="usia" :value="'Umur'" />
                                        <x-input-text id="usia" name="usia" type="number"
                                            class="mt-2 block w-full" :value="old(
                                                'usia',
                                                isset($dmti) && $dmti->user_id == $user->id ? $dmti->usia : '',
                                            )" required placeholder="25" />
                                        <x-input-error class="mt-1" :messages="$errors->get('usia')" />
                                    </div>
                                    <div>
                                        <x-input-label for="no_hp" :value="'No. Handphone'" />
                                        <x-input-text id="no_hp" name="no_hp" type="number"
                                            class="mt-2 block w-full" :value="old(
                                                'no_hp',
                                                isset($dmti) && $dmti->user_id == $user->id ? $dmti->no_hp : '',
                                            )" required
                                            placeholder="08xxxxxxxxx" />
                                        <x-input-error class="mt-1" :messages="$errors->get('no_hp')" />
                                    </div>
                                </div>
                                <div class="mt-3 text-sm">
                                    <p>
                                        Telah berobat di Klinik Polbangtan Malang pada hari <span id="today"></span>
                                        tanggal <span id="datenow"></span> dengan
                                        diagnosa <input type="text" name="diagnosa" id="diagnosa"
                                            value="{{ $rm->diagnosa }}"
                                            class="p-1 w-full text-sm rounded-lg bg-gray-50 border border-gray-300 text-gray-900 font-semibold focus:ring-blue-600 focus:border-blue-600 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            required /></span>
                                    </p>
                                    <br>
                                    <p>
                                        Demikian surat keterangan ini kami buat dan dapat dipertanggung jawabkan
                                        sebagaimana mestinya.
                                    </p>
                                </div>
                            </div>
                            <div class="p-3 mt-3 bg-gray-100 dark:bg-dark rounded">
                                <h2 class="text-lg font-medium">Terapi / Permintaan Obat</h2>
                                <ul class="list-decimal px-3 mt-1 text-sm text-gray-700 dark:text-gray-300">
                                    @foreach ($tindakan['nama_obat'] as $index => $nama_obat)
                                        <li>{{ $nama_obat }} {{ $tindakan['jumlah_obat'][$index] }}x</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="flex items-start my-3">
                                <div class="flex items-center h-5">
                                    <input id="remember" type="checkbox" value=""
                                        class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800"
                                        required />
                                </div>
                                <label for="remember"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Saya
                                    setuju bahwa
                                    data ini benar dan sesuai. <span class="text-red-500">Tidak dapat melakukan
                                        perubahan dokumen data setelah di simpan!</span></label>
                            </div>
                            <x-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 text-right pb-3">
        @include('kesehatan.partials.modals.batal')
        <div class="max-w-screen-xl mx-auto flex gap-2 justify-end">
            <x-danger-button class="py-2 px-4" type="button"
                x-on:click.prevent="$dispatch('open-modal', 'batal');">{{ __('Batal') }}</x-danger-button>
        </div>
    </div>
    <script src="{{ asset('src/js/kesehatan/surat-laporan.js') }}"></script>
</x-app-layout>
