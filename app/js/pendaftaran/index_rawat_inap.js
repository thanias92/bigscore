$(function () {
  // begin:: table rawat inap
  var btnPilihAksiRawatInap = function (cell, formatterParams, onRendered) {
    return `<div class="btn-group">
              <button class="btn btn-xs btn-soft-secondary dropdown-toggle btn_pilih_aksi" type="button" data-bs-toggle="dropdown" aria-expanded="true">
              Pilih Aksi
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <li><a class="dropdown-item" href="#">Daftar Walk In</a></li>
                  <li><a class="dropdown-item" href="#">Daftar Booking Online</a></li>
                  <li><a class="dropdown-item" href="#">Riwayat Medis</a></li>
                  <li><a class="dropdown-item" href="#">Detail Pasien</a></li>
              </ul>
          </div>`;
  };

  table_rawat_inap = new Tabulator("#table_rawat_inap", {
    layout: "fitColumns",
    placeholder: "Belum ada data pasien.",
    pagination: true, //enable pagination
    paginationSize: 10,
    paginationMode: "remote", //enable remote pagination
    ajaxURL: $("#url_data_pasien").data("url"), //set url for ajax request
    dataSendParams: {
      page: "pageNo",
    },
    columns: [
      {
        title: "No. RM",
        field: "no_rekam_medis",
        headerSort: false,
        hozAlign: "center",
        headerHozAlign: "center",
        width: 100,
      },
      {
        title: "Nama Lengkap",
        field: "nama_lengkap",
        headerSort: false,
        width: 250,
      },
      {
        title: "Tempat & Tgl. Lahir",
        field: "tempat_lahir",
        headerSort: false,
        width: 250,
      },
      { title: "Alamat Domisili", field: "alamat_domisili", headerSort: false },
      {
        title: "No. KTP",
        field: "no_identitas",
        headerSort: false,
        width: 150,
        headerHozAlign: "center",
        hozAlign: "center",
      },
      {
        title: "No. HP",
        field: "no_hp",
        headerSort: false,
        hozAlign: "center",
        width: 150,
        headerHozAlign: "center",
      },
      {
        title: "",
        width: 100,
        formatter: btnPilihAksiRawatInap,
        hozAlign: "center",
        headerHozAlign: "center",
        headerSort: false,
      },
    ],
  });
  // end:: table rawat inap
});
