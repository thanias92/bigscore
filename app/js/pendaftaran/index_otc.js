$(function () {
  // begin:: aksi filter periode transaksi
  var periode_transaksi_otc = $(".periode_transaksi_otc").flatpickr({
    mode: "range",
    dateFormat: "d-m-Y",
    onClose: function (selectedDates, dateStr, instance) {
      table_otc.setData($("#url_data_otc").data("url"), {
        periode_transaksi: $(".periode_transaksi_otc").val(),
        nama_pembeli: $(".nama_pembeli_otc").val(),
      });
    },
  });
  $(".reset_periode_transaksi_otc").on("click", function () {
    periode_transaksi_otc.clear();
    table_otc.setData($("#url_data_otc").data("url"), {
      periode_transaksi: $(".periode_transaksi_otc").val(),
      nama_pembeli: $(".nama_pembeli_otc").val(),
    });
  });
  // end:: aksi filter periode transaksi

  // begin:: aksi filter nama pembeli
  $(".nama_pembeli_otc").on("keydown", function () {
    delayTyping(function () {
      table_otc.setData($("#url_data_otc").data("url"), {
        periode_transaksi: $(".periode_transaksi_otc").val(),
        nama_pembeli: $(".nama_pembeli_otc").val(),
      });
    }, 500);
  });
  // end:: aksi filter nama pembeli

  // begin:: table otc
  var btnAksiObatBebas = function (cell, formatterParams, onRendered) {
    return `<div class="btn-group">
                  <button class="btn btn-xs btn-soft-secondary dropdown-toggle btn_pilih_aksi" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                  Pilih Aksi
                  </button>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <li><a class="dropdown-item" href="#">Detail</a></li>
                      <li><a class="dropdown-item" href="#">Bill</a></li>
                  </ul>
              </div>`;
  };

  table_otc = new Tabulator("#table_otc", {
    layout: "fitColumns",
    placeholder: "Belum ada data.",
    pagination: true, //enable pagination
    paginationSize: 10,
    paginationMode: "remote", //enable remote pagination
    ajaxURL: $("#url_data_otc").data("url"), //set url for ajax request
    // ajaxParams: {
    //   periode_transaksi: $(".periode_transaksi_otc").val(),
    //   nama_pembeli: $(".nama_pembeli_otc").val(),
    // },
    dataSendParams: {
      page: "pageNo",
    },
    columns: [
      {
        title: "Waktu Transaksi",
        field: "waktu_registrasi",
        headerSort: false,
        formatter: "datetime",
        formatterParams: {
          outputFormat: "dd/MM/yyyy HH:ss",
          invalidPlaceholder: "(invalid date)",
        },
      },
      { title: "No. Registrasi", field: "no_registrasi", headerSort: false },
      {
        title: "Nama Lengkap",
        field: "registrasiPasien.nama_lengkap",
        headerSort: false,
      },
      {
        title: "No. HP",
        field: "registrasiPasien.no_hp",
        headerSort: false,
      },
      { title: "Obat", field: "alamat_domisili", headerSort: false },
      { title: "Alkes", field: "no_identitas", headerSort: false },
      { title: "Pembayaran", field: "no_hp", headerSort: false },
      { title: "Status", field: "no_hp", headerSort: false },
      {
        title: "",
        width: 100,
        formatter: btnAksiObatBebas,
        hozAlign: "center",
        headerSort: false,
      },
    ],
  });
  // end:: table otc
});
