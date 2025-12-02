<?php

use Carbon\Carbon;


//data pasien
$jenis_kelamin = $registrasi->registrasiPasien?->jenis_kelamin; //jenis kelamin pasien
$tempat_lahir_pasien = $registrasi->registrasiPasien?->tempat_lahir;
//custom format tanggal lahir
$tanggal_lahir = $registrasi->registrasiPasien?->tanggal_lahir; //tanggal lahir

if ($tanggal_lahir === null) {
    $tgl_lahir_pasien = "-";
    $usia_tahun = "-";
    $usia_bulan = "-";
    $usia_hari = "-";
} else {
    // Assuming $tanggal_lahir is your date string in 'Y-m-d' format
    $dateTime = Carbon::createFromFormat('Y-m-d', $tanggal_lahir);
    $tgl_lahir_pasien = $dateTime->format('d-M-Y');

    // hitung umur bulan dan hari
    // Calculate age
    $age = $dateTime->diff(Carbon::now());

    $usia_tahun = $age->y;
    $usia_bulan = $age->m;
    $usia_hari = $age->d;
}

$usia = $usia_tahun . " Tahun / " . $usia_bulan . " Bulan / " . $usia_hari . " Hari";
//
$penjamin = $registrasi->jaminanPasiens?->penjamin->role; //nama instansi penjamin
if ($penjamin === 'Penjamin') {
    $nama_instansi_penjamin = $registrasi->jaminanPasiens?->penjamin?->instansi->nama_instansi;
} else {
    $nama_instansi_penjamin = 'UMUM';
}
// $nama_instansi_penjamin = $registrasi->registrasiPasien?->pasienPenjamin->penjamin->instansi->nama_instansi; //nama instansi penjamin
$alamat_pasien = $registrasi->registrasiPasien?->alamat; //alamat pasien
$no_medis = $registrasi->registrasiPasien?->no_rekam_medis; // nomor rekam medis
$nama_lengkap = $registrasi->registrasiPasien?->nama_lengkap; // nama lengkap
$tgl_lahir = $registrasi->registrasiPasien?->tanggal_lahir; //tanggal lahir
$agama = $registrasi->registrasiPasien?->agama->nama; //nama agama pasien
$no_telp = $registrasi->registrasiPasien?->no_hp; //no hp pasien
$pendidikan = $registrasi->registrasiPasien?->id_pendidikan; //pendidikan pasien
$pendidikan_pasien = ($pendidikan == 0 ? '-' : ($pendidikan == 1 ? 'SD' : ($pendidikan == 2 ? 'SLTP' : ($pendidikan == 3 ? 'SLTA' : ($pendidikan == 4 ? 'D1-D3' : ($pendidikan == 5 ? 'D4' : ($pendidikan ==  6 ? 'S1' : ($pendidikan == 7 ? 'S2' : ($pendidikan == 8 ? 'S3' : '')))))))));

$pekerjaan = $registrasi->registrasiPasien?->id_pekerjaan; //pekerjaan pasien
$pekerjaan_pasien = ($pekerjaan == null ? '-' : ($pekerjaan == 1 ? 'Pns/ASN' : ($pekerjaan == 2 ? 'TNI/Polri' : ($pekerjaan == 3 ? 'BUMN' : ($pekerjaan == 4 ? 'Pegawai Swasta' : ($pekerjaan == 5 ? 'Lain-Lain' : 'Tidak Bekerja'))))));

//data departemen departemen
$nama_departemen = $registrasi->departemen?->nama_departemen; //nama departemen

//data dokter
$nama_lengkap_dokter = $registrasi->dpjps?->dokter->nama_lengkap; // nama_lengkap_dokter
$jenis_dpjp = $registrasi->dpjps?->jenis_dpjp; //jenis dpjp 

//data registrasi
$formattedNoRegis = $registrasi->registrasiPasien?->no_rekam_medis;
// $noregis = $registrasi->registrasiPasien?->no_rekam_medis;

// Memeriksa apakah nomor rekam medis ada
if ($formattedNoRegis !== null) {
    // Membagi string menjadi potongan-potongan setiap 3 karakter
    $noregis = chunk_split($formattedNoRegis, 2, '-');

    // Menghapus karakter '-' yang mungkin terdapat di akhir string
    $noregis = rtrim($noregis, '-');

    // Menampilkan nomor rekam medis dengan format
    $noregis;
} else {
    "-";
}
$waktu_regis = $registrasi?->waktu_registrasi;
$waktu_regis = $waktu_regis ?? time();

// Konversi ke integer jika masih dalam bentuk string
$timestamp = is_numeric($waktu_regis) ? (int)$waktu_regis : strtotime($waktu_regis);
// Format waktu
$waktu = date('d/M/Y', $timestamp);


?>
<fieldset>
    <div class="card shadow-sm border">
        <div class="row h6 m-0 p-2" id="toggle-detail">
            <div class="row">
                <div class="col-lg-3 d-flex align-items-center">
                    <p class="mt-3"><?= '<strong>' .  $nama_lengkap . '</strong>' . '/ ' . ($jenis_kelamin == '1' ? 'Laki-Laki' : 'Perempuan') . '/' . $usia_tahun . ' Tahun ' ?></p>
                </div>
                <div class="col-lg-3 d-flex align-items-center">
                    <p class="mt-3"><?= '<strong>' . $waktu . '</strong>' . '/' . $noregis . '/' . $nama_instansi_penjamin ?></p>
                </div>
                <div class="col-lg-4 d-flex align-items-center">
                    <p class="mt-3"><?= '<strong>' . $nama_departemen . ' / </strong>' . $nama_lengkap_dokter ?></p>
                </div>
                <div class="col-lg-2  d-flex align-items-center">
                    <button type="button" class="btn btn-sm border btn-primary rounded-pill px-4" id="toggle-tampil">
                        Tampilkan Detail &nbsp;<i class="fas fa-arrow-down"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row p-2 pt-3 pb-3 collapse m-0 h6" id="detail-data">

            <div class="col-lg-4">
                <div class="row h6">
                    <div class="col-lg-5">
                        <p class="mb-0">Nama Pasien</p>
                        <p class="mb-0">Tempat & Tgl Lahir</p>
                        <p class="mb-0">Agama</p>
                        <p class="mb-0">Umur</p>
                        <p class="mb-0">Marital</p>
                        <p class="mb-0">Alamat</p>
                        <p class="mb-0">Alergi</p>
                        <p class="mb-0">Catatan</p>
                    </div>
                    <div class="col-lg-7 h6">
                        <p class="mb-0"><?= ': ' . $nama_lengkap . ' / ' . ($jenis_kelamin == '1' ? 'Laki-Laki' : 'Perempuan') . '' ?></p>
                        <p class="mb-0"><?= ': ' . $tempat_lahir_pasien . ' ' . $tgl_lahir_pasien . '' ?></p>
                        <p class="mb-0"><?= ': ' . ($agama != null ? $agama : '-') . '' ?></p>
                        <p class="mb-0"><?= ': ' . $usia  ?></p>
                        <p class="mb-0"><?= ': ' . '-' ?></p>
                        <p class="mb-0"><?= ': ' . $alamat_pasien . '' ?></p>
                        <p class="mb-0"><?= ': ' . '-' ?></p>
                        <p class="mb-0"><?= ': ' . ($registrasi?->catatan != null ? $registrasi->catatan : '-') . '' ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-6">
                        <p class="mb-0">No Telp</p>
                        <p class="mb-0">Pendidikan</p>
                        <p class="mb-0">Pekerjaan</p>
                        <p class="mb-0">No Daftar</p>
                        <p class="mb-0">No.Mr</p>
                        <p class="mb-0">Mr Internal</p>
                        <p class="mb-0">Jenis Poli</p>
                    </div>
                    <div class="col-lg-6 h6">
                        <p class="mb-0"><?= ': ' . ($no_telp != null ? $no_telp : '-') . '' ?></p>
                        <p class="mb-0"><?= ': ' . $pendidikan_pasien . '' ?></p>
                        <p class="mb-0"><?= ': ' . $pekerjaan_pasien . '' ?></p>
                        <p class="mb-0"><?= ': ' . $noregis . '' ?></p>
                        <p class="mb-0"><?= ': ' . '-' ?></p>
                        <p class="mb-0"><?= ': ' . '-' ?></p>
                        <p class="mb-0"><?= ': ' . $nama_departemen . '' ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-6">
                        <p class="mb-0">Tgl Layanan</p>
                        <p class="mb-0">jenis Pembayaran</p>
                        <p class="mb-0">Dokter</p>
                        <p class="mb-0">Perawat</p>
                        <p class="mb-0">Shift</p>
                        <p class="mb-0">Status</p>
                        <p class="mb-0">No Antrian</p>
                    </div>
                    <div class="col-lg-6 h6">
                        <p class="mb-0"><?= ': ' . $waktu . '' ?></p>
                        <p class="mb-0"><?= ': ' . $nama_instansi_penjamin . '' ?></p>
                        <p class="mb-0"><?= ': ' . $nama_lengkap_dokter . '' ?></p>
                        <p class="mb-0"><?= ': ' . $nama_lengkap_dokter . '' ?></p>
                        <p class="mb-0"><?= ': ' . '- ' ?> </strong></p>
                        <p class="mb-0"><?= '<strong style="color: ' . ($registrasi->status_registrasi == true ? '#35B700' : '#F92C00') . '"> : ' . ($registrasi->status_registrasi == true ? 'Open' : 'Close') . '</strong>' ?></p>
                        <p class="mb-0"><?= ': ' . '-' ?></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <button type="button" class="btn btn-sm border btn-primary w-100 rounded-pill" id="toggle-hidden">
                        Sembunyikan Detail &nbsp;<i class="fas fa-arrow-up"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

</fieldset>

<script>
    document.getElementById('toggle-tampil').addEventListener('click', function() {
        document.getElementById('toggle-detail').style.display = 'none';
        document.getElementById('detail-data').classList.toggle('collapse');
        document.getElementById('toggle-tampil').style.display = 'none';
        document.getElementById('toggle-hidden').style.display = 'block';
        // Toggle visibility of "sembunyikan detail" button
    });

    document.getElementById('toggle-hidden').addEventListener('click', function() {
        document.getElementById('toggle-detail').style.display = 'block';
        document.getElementById('detail-data').classList.toggle('collapse');
        document.getElementById('toggle-hidden').style.display = 'none';
        document.getElementById('toggle-tampil').style.display = 'block';
    });
</script>