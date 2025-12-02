$(function () {
  var table_jadwal_praktek;

  // begin:: buat jadwal praktek
  $("body").on("click", ".btn_buat_jadwal", function () {
    modalLarge(
      $("#url_buat_jadwal").data("url"),
      {},
      "BUAT JADWAL PRAKTEK",
      "GET"
    );
  });
  // end:: buat jadwal praktek

  var url_jadwal_praktek = $("#url_daftar_jadwal").data("url");

  // begin:: aksi filter periode registrasi

  // end:: aksi filter nama pasien

  // begin:: table jadwal praktek
  var btnAksiJadwalPraktek = function (cell, formatterParams, onRendered) {
    return `<button class="btn btn-xs btn-soft-success btn_konfirmasi_booking" type="button" data-id_pasien="${
      cell.getData().id
    }">
                  Konfirmasi
              </button>`;
  };

  table_jadwal_praktek = new Tabulator("#table_jadwal_praktek", {
    layout: "fitColumns",
    placeholder: "Belum ada data.",
    pagination: true, //enable pagination
    paginationSize: 10,
    paginationMode: "remote", //enable remote pagination
    ajaxURL: url_jadwal_praktek, //set url for ajax request
    dataSendParams: {
      page: "pageNo",
    },
    columns: [
      {
        title: "Tgl. Booking",
        field: "tanggal_perjanjian",
        width: 90,
        headerSort: false,
        hozAlign: "center",
        headerHozAlign: "center",
        formatter: "datetime",
        formatterParams: {
          outputFormat: "dd/MM/yyyy",
        },
      },
      {
        title: "Kode Booking",
        field: "kode_booking",
        headerSort: false,
      },
      {
        title: "Pasien",
        headerSort: false,
        formatter: function (cell) {
          var rowData = cell.getRow().getData();
          var html = "";
          html += "Nama: <b>" + rowData.pasien.nama_lengkap + "</b><br/>";
          html += "No. RM: <b>" + rowData.pasien.no_rekam_medis + "</b>";

          return html;
        },
      },
      {
        title: "Asal Booking",
        field: "asal_perjanjian",
        headerSort: false,
      },
      { title: "Poli/ Layanan", field: "asal_perjanjian", headerSort: false },
      {
        title: "Dokter",
        field: "asal_perjanjian",
      },
      {
        title: "",
        width: 100,
        formatter: btnAksiJadwalPraktek,
        hozAlign: "center",
        headerHozAlign: "center",
        headerSort: false,
      },
    ],
  });
  // end:: table jadwal praktek
});
