$(function () {
  // begin:: table pasien
  var btnPilihAksi = function (cell, formatterParams, onRendered) {
    let id_pasien = cell.getData().id;
    return `<div class="btn-group">
                <button class="btn btn-xs btn-soft-secondary dropdown-toggle btn_pilih_aksi" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                Pilih Aksi
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item aksi_daftar_walkin" data-id="${id_pasien}" href="#">Daftar Walk In</a></li>
                    <li><a class="dropdown-item aksi_daftar_booking" data-id="${id_pasien}" href="#">Daftar Booking Online</a></li>
                    <li><a class="dropdown-item" data-id="${id_pasien}" href="#">Riwayat Medis</a></li>
                    <li><a class="dropdown-item" data-id="${id_pasien}" href="#">Detail Pasien</a></li>
                </ul>
            </div>`;
  };

  table_pasien = new Tabulator("#table_pasien", {
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
        width: 100,
        headerHozAlign: "center",
      },
      {
        title: "Pasien",
        headerSort: false,
        formatter: function (cell) {
          var rowData = cell.getRow().getData();
          var html = "";
          html += "Nama: <b>" + rowData.nama_lengkap + "</b><br/>";
          html += "No. KTP: <b>" + rowData.no_identitas + "</b>";

          return html;
        },
      },
      {
        title: "Tempat & Tgl. Lahir",
        headerSort: false,
        formatter: function (cell) {
          var rowData = cell.getRow().getData();
          var html = "";
          html += rowData.tempat_lahir + "<br/>";
          html += rowData.tanggal_lahir;

          return html;
        },
      },
      {
        title: "Alamat Domisili",
        field: "alamat_domisili",
        headerSort: false,
        formatter: "textarea",
      },
      {
        title: "No. HP",
        field: "no_hp",
        headerSort: false,
        hozAlign: "center",
        width: 100,
        headerHozAlign: "center",
      },
      {
        title: "",
        width: 90,
        formatter: btnPilihAksi,
        hozAlign: "center",
        headerHozAlign: "center",
        headerSort: false,
      },
    ],
  });
  // end:: table pasien
});
