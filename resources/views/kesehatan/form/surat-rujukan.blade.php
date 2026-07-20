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
                        <form action="{{ route('kesehatan.form.store.sr', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                <div class="text-sm font-medium italic">
                                    Merujuk kepada:
                                </div>
                                <div class="grid gap-2 md:grid-cols-2 mt-2">
                                    <div>
                                        <x-input-label for="nama_dokter" :value="'Nama Dokter'" />
                                        <x-input-text id="nama_dokter" name="nama_dokter" type="text"
                                            class="mt-2 block w-full" :value="Auth::user()->name" required
                                            placeholder="dr. Example" />
                                        <x-input-error class="mt-1" :messages="$errors->get('nama_dokter')" />
                                    </div>
                                    <div id="select-rs">
                                        <x-input-label for="nama_rs">Cari RS</x-input-label>
                                        <div x-data="selectConfigs()" x-init="fetchOptions()"
                                            class="flex flex-col items-center relative">
                                            <x-input-text @click.away="close()" x-model="filter"
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0" @mousedown="open()"
                                                @keydown.enter.stop.prevent="selectOption()"
                                                @keydown.arrow-up.prevent="focusPrevOption()"
                                                @keydown.arrow-down.prevent="focusNextOption()" type="search"
                                                id="nama_rs" name="nama_rs" class="mt-2 block w-full"
                                                placeholder="Cari RS" />
                                            <div x-show="isOpen()"
                                                class="absolute shadow-lg bg-white dark:bg-darker top-100 z-40 w-full left-0 top-11 rounded max-h-[150px] overflow-y-auto">
                                                <div class="flex flex-col w-full">
                                                    <template x-for="(option, index) in filteredOptions()"
                                                        :key="index">
                                                        <div @click="onOptionClick(index)"
                                                            :class="classOption(option.nama_rs, index)"
                                                            :aria-selected="focusedOptionIndex === index">
                                                            <div class="w-full items-center p-2 pl-2 border-transparent border-l-2 relative hover:border-teal-100 dark:hover:border-darkerhover">
                                                                <div class="w-full items-center flex">
                                                                    <span x-text="option.nama_rs" id="nama_rs_span"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="rs_id" id="rs_id">
                                        {{-- <x-button class="py-2 px-4 mt-2" type="button" @click="configs.resetRS()">Reset Daftar RS</x-button> --}}
                                    </div>
                                </div>
                                <div class="mt-3 bg-gray-100 dark:bg-dark rounded">
                                    <div class="text-sm font-medium italic">
                                        Mohon pemeriksaan dan pengobatan lebih lanjut terhadap penderita:
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
                                                class="mt-2 block w-full" :value="old('name', isset($user) ? $user->name : '')" required
                                                placeholder="Alex" />
                                            <x-input-error class="mt-1" :messages="$errors->get('nama_pasien')" />
                                        </div>
                                        @php
                                            $tanggalLahirFormatted =
                                                isset($dmti) && $dmti->user_id == $user->id
                                                    ? \Carbon\Carbon::parse($dmti->tanggal_lahir)->translatedFormat(
                                                        'd F Y',
                                                    )
                                                    : '';
                                            $tempatKelahiran =
                                                isset($dmti) && $dmti->user_id == $user->id
                                                    ? $dmti->tempat_kelahiran
                                                    : '';
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
                                                )" required
                                                placeholder="25" />
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
                                </div>
                                <div class="mt-3 bg-gray-100 dark:bg-dark rounded">
                                    <div class="text-sm font-medium italic">
                                        Anamnese:
                                    </div>
                                    <div class="grid gap-2 md:grid-cols-2 mt-2">
                                        <div>
                                            <x-input-label for="keluhan" :value="'Keluhan'" />
                                            <textarea name="keluhan" id="cek_fisik" cols="5" rows="5" required
                                                class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                            <x-input-error class="mt-1" :messages="$errors->get('keluhan')" />
                                        </div>
                                        <div>
                                            <x-input-label for="diagnosa" :value="'Diagnosa Sementara'" />
                                            <textarea name="diagnosa" id="diagnosa" cols="5" rows="5" required
                                                class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                            <x-input-error class="mt-1" :messages="$errors->get('diagnosa')" />
                                        </div>
                                        <div>
                                            <x-input-label for="kasus" :value="'Kasus'" />
                                            <textarea name="kasus" id="kasus" cols="5" rows="5" required
                                                class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                            <x-input-error class="mt-1" :messages="$errors->get('kasus')" />
                                        </div>
                                        <div>
                                            <x-input-label for="tindakan" :value="'Terapi/Obat yang telah diberikan'" />
                                            <textarea name="tindakan" id="tindakan" cols="5" rows="5" required
                                                class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                            <x-input-error class="mt-1" :messages="$errors->get('tindakan')" />
                                        </div>
                                    </div>
                                    <div class="mt-3 text-sm">
                                        <p>
                                            Demikian surat rujukan ini kami kirim, kami mohon balasan atas surat rujukan
                                            ini. Atas perhatian Bapak/Ibu kami ucapkan terima kasih.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start my-3">
                                    <div class="flex items-center h-5">
                                        <input id="remember-surat" type="checkbox" value=""
                                            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800"
                                            required />
                                    </div>
                                    <label for="remember-surat"
                                        class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Saya setuju
                                        bahwa
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
            {{-- <form action="" method="post">
                <x-button class="py-2 px-4" type="submit">{{ __('Lanjut') }}</x-button>
            </form> --}}
        </div>
        <div id="url_histori_rs" class="hidden"></div>
        <script>
            const url = '{{ route('api.daftarRS') }}';
            const element = document.getElementById('url_histori_rs');
            element.textContent = url;
        </script>
    </div>
    <script src="{{ asset('src/js/kesehatan/daftar-rs.js') }}"></script>
    {{-- <script src="{{ asset('src/js/kesehatan/surat-laporan.js') }}"></script> --}}
</x-app-layout>
