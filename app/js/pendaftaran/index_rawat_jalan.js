$(function () {
  var url_rajal = $("#url_data_rajal").data("url");

  // begin:: aksi filter periode registrasi
  var periode_registrasi_rajal = $(".filter_periode_rawat_jalan").flatpickr({
    mode: "range",
    dateFormat: "d-m-Y",
    onClose: function (selectedDates, dateStr, instance) {
      table_rawat_jalan.setData(url_rajal, {
        periode_registrasi: $(".filter_periode_rawat_jalan").val(),
        nama_pasien: $(".filter_nama_pasien_rawat_jalan").val(),
        id_dokter: $(".filter_dokter_rawat_jalan").val(),
        id_penjamin: $(".filter_penjamin_rawat_jalan").val(),
      });
    },
  });
  $(".reset_filter_periode_rawat_jalan").on("click", function () {
    periode_registrasi_rajal.clear();
    table_rawat_jalan.setData(url_rajal, {
      periode_registrasi: $(".filter_periode_rawat_jalan").val(),
      nama_pasien: $(".filter_nama_pasien_rawat_jalan").val(),
      id_dokter: $(".filter_dokter_rawat_jalan").val(),
      id_penjamin: $(".filter_penjamin_rawat_jalan").val(),
    });
  });
  // end:: aksi filter periode transaksi

  // begin:: aksi filter nama pasien
  $(".filter_nama_pasien_rawat_jalan").on("keydown", function () {
    delayTyping(function () {
      table_rawat_jalan.setData(url_rajal, {
        periode_registrasi: $(".filter_periode_rawat_jalan").val(),
        nama_pasien: $(".filter_nama_pasien_rawat_jalan").val(),
        id_dokter: $(".filter_dokter_rawat_jalan").val(),
        id_penjamin: $(".filter_penjamin_rawat_jalan").val(),
      });
    }, 500);
  });
  // end:: aksi filter nama pasien

  //begin:: aksi filter dokter
  $(".filter_dokter_rawat_jalan").on("change", function () {
    table_rawat_jalan.setData(url_rajal, {
      periode_registrasi: $(".filter_periode_rawat_jalan").val(),
      nama_pasien: $(".filter_nama_pasien_rawat_jalan").val(),
      id_dokter: $(".filter_dokter_rawat_jalan").val(),
      id_penjamin: $(".filter_penjamin_rawat_jalan").val(),
    });
  });
  //end:: aksi filter dokter

  //begin:: aksi filter penjamin
  $(".filter_penjamin_rawat_jalan").on("change", function () {
    table_rawat_jalan.setData(url_rajal, {
      periode_registrasi: $(".filter_periode_rawat_jalan").val(),
      nama_pasien: $(".filter_nama_pasien_rawat_jalan").val(),
      id_dokter: $(".filter_dokter_rawat_jalan").val(),
      id_penjamin: $(".filter_penjamin_rawat_jalan").val(),
    });
  });
  //end:: aksi filter penjamin

  // begin:: table rawat jalan
  var btnPilihAksiRawatJalan = function (cell, formatterParams, onRendered) {
    return `<div class="btn-group">
                <button class="btn btn-xs btn-soft-secondary dropdown-toggle btn_pilih_aksi" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                Pilih Aksi
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="#">Detail & Info Pendaftaran</a></li>
                    <li><a class="dropdown-item" href="#">EMR</a></li>
                    <li><a class="dropdown-item" href="#">Daftarkan Rawat Inap</a></li>
                    <li><a class="dropdown-item" href="#">Cetak Bukti Pendaftaran</a></li>
                    <li><a class="dropdown-item" href="#">Farmasi</a></li>
                    <li><a class="dropdown-item" href="#">Bill/ Tagihan</a></li>
                </ul>
            </div>`;
  };

  table_rawat_jalan = new Tabulator("#table_rawat_jalan", {
    layout: "fitColumns",
    responsiveLayout:"collapse",
    placeholder: "Belum ada data rawat jalan.",
    pagination: true, //enable pagination
    paginationSize: 10,
    paginationMode: "remote", //enable remote pagination
    ajaxURL: $("#url_data_rajal").data("url"), //set url for ajax request
    dataSendParams: {
      page: "pageNo",
    },
    columns: [
      {
        title: "No. Antrian",
        field: "no_rekam_medis",
        headerSort: false,
        hozAlign: "center",
        headerHozAlign: "center",
        width: 80,
      },
      {
        title: "Tgl. Kunjungan",
        width: 100,
        field: "waktu_registrasi",
        headerSort: false,
        hozAlign: "center",
        headerHozAlign: "center",
        formatter: "datetime",
        formatterParams: {
          outputFormat: "dd/MM/yyyy",
        },
      },
      {
        title: "Pasien",
        headerSort: false,
        formatter: function (cell) {
          var rowData = cell.getRow().getData();
          var html = "";
          html +=
            "Nama lengkap: <b>" +
            rowData.registrasiPasien.pasien.nama_lengkap +
            "</b><br/>";
          html +=
            "No. RM: <b>" + rowData.registrasiPasien.no_rekam_medis + "</b>";

          return html;
        },
      },
      {
        title: "Detail Kunjungan",
        headerSort: false,
        formatter: function (cell) {
          var rowData = cell.getRow().getData();
          var html = "";
          html += "No. Registrasi: <b>" + rowData.no_registrasi + "</b><br/>";
          if (rowData.departemen) {
            html +=
              "Poli/ Layanan: <b>" +
              rowData.departemen.nama_departemen +
              "</b><br/>";
          } else {
            html += "Poli/ Layanan: <b>-</b><br/>";
          }
          html +=
            "Dokter: <b>" + rowData.dpjpUtama.dokter.nama_lengkap + "</b>";

          return html;
        },
      },
      {
        title: "Pembayaran",
        headerSort: false,
        formatter: function (cell, formatterParams, onRendered) {
          var rowData = cell.getRow().getData();
          var html = "";
          html +=
            "Penjamin: <b>" +
            rowData.jaminanPasienUtama.penjamin.instansi.nama_instansi +
            "</b><br/>";
          html += "Total: <b>-</b><br/>";
          html +=
            "Status bayar: <b>" +
            rowData.registrasiBill.status_pembayaran +
            "</b><br/>";

          return html;
        },
      },
      {
        title: "Status",
        headerSort: false,
        formatter: function (cell) {
          var rowData = cell.getRow().getData();
          var html = "";
          html += "Poli: -<br/>";
          html += "Farmasi: -<br/>";
          html += "Kasir: -<br/>";

          return html;
        },
      },
      {
        title: "",
        width: 80,
        formatter: btnPilihAksiRawatJalan,
        hozAlign: "center",
        headerHozAlign: "center",
        headerSort: false,
      },
    ],
  });
  // end:: table rawat jalan
});
