<div class="w-full">
    <x-input-label for="nik" :value="'NIK'" />
    <x-input-text id="nik" name="nik" type="number" class="mt-2 block w-full" :value="old('nik', isset($dmti) ? $dmti->nik : '')" required
        autofocus placeholder="350707xxxxxxxxxx" />
    <x-input-error class="mt-1" :messages="$errors->get('nik')" />
</div>
<div>
    <x-input-label for="bpjs" :value="'No. BPJS'" />
    <x-input-text id="bpjs" name="bpjs" type="text" class="mt-2 block w-full" :value="old('bpjs', isset($dmti) ? $dmti->no_bpjs : '')" required
        autofocus placeholder="000145xxxxxxxx" />
    <x-input-error class="mt-1" :messages="$errors->get('bpjs')" />
</div>
<div>
    <x-input-label for="hp" :value="'No. Handphone'" />
    <x-input-text id="hp" name="hp" type="text" class="mt-2 block w-full" :value="old('hp', isset($dmti) ? $dmti->no_hp : '')" required
        autofocus placeholder="081235xxxxxx" />
    <x-input-error class="mt-1" :messages="$errors->get('hp')" />
</div>
<div>
    <x-input-label for="tempat_kelahiran" :value="'Tempat Kelahiran'" />
    <x-input-text id="tempat_kelahiran" name="tempat_kelahiran" type="text" class="mt-2 block w-full"
        :value="old('tempat_kelahiran', isset($dmti) ? $dmti->tempat_kelahiran : '')" placeholder="Tempat Kelahiran" />
    <x-input-error class="mt-1" :messages="$errors->get('tempat_kelahiran')" />
</div>
<div>
    <x-input-label for="tanggal_lahir" :value="'Tanggal Lahir'" />
    <x-input-text id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-2 block w-full" :value="old('tanggal_lahir', isset($dmti) ? $dmti->tanggal_lahir : '')"
        required autofocus />
    <x-input-error class="mt-1" :messages="$errors->get('tanggal_lahir')" />
</div>
<div>
    <x-input-label for="jenis_kelamin" :value="'Jenis Kelamin'" />
    <select id="jenis_kelamin" name="jenis_kelamin"
        class="block w-full mt-2 p-2 border border-gray-300 rounded-lg bg-white dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option value="pria"
            {{ old('jenis_kelamin', isset($dmti) && $dmti->jenis_kelamin == 'pria' ? 'selected' : '') }}>Pria</option>
        <option value="wanita"
            {{ old('jenis_kelamin', isset($dmti) && $dmti->jenis_kelamin == 'wanita' ? 'selected' : '') }}>Wanita
        </option>
    </select>
    <x-input-error class="mt-1" :messages="$errors->get('jenis_kelamin')" />
</div>
<div>
    <x-input-label for="golongan_darah" :value="'Golongan Darah'" />
    <x-input-text id="golongan_darah" name="golongan_darah" type="text" class="mt-2 block w-full" :value="old('golongan_darah', isset($dmti) ? $dmti->golongan_darah : '')"
        placeholder="Golongan Darah" />
    <x-input-error class="mt-1" :messages="$errors->get('golongan_darah')" />
</div>
<div>
    <x-input-label for="usia" :value="'Usia'" />
    <x-input-text id="usia" name="usia" type="number" class="mt-2 block w-full cursor-not-allowed"
        :value="old('usia', isset($dmti) ? $dmti->usia : '')" placeholder="Otomatis" required autofocus readonly disabled />
    <x-input-error class="mt-1" :messages="$errors->get('usia')" />
</div>
