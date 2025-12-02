$(function () {
  var url_booking = $("#url_data_booking").data("url");

  // begin:: aksi filter periode registrasi
  var periode_registrasi_rajal = $(".filter_periode_booking").flatpickr({
    mode: "range",
    dateFormat: "d-m-Y",
    onClose: function (selectedDates, dateStr, instance) {
      table_booking.setData(url_booking, {
        periode_registrasi: $(".filter_periode_booking").val(),
        nama_pasien: $(".filter_nama_pasien_booking").val(),
        id_dokter: $(".filter_dokter_booking").val(),
        id_penjamin: $(".filter_penjamin_booking").val(),
      });
    },
  });
  $(".reset_filter_periode_booking").on("click", function () {
    periode_registrasi_rajal.clear();
    table_booking.setData(url_booking, {
      periode_registrasi: $(".filter_periode_booking").val(),
      nama_pasien: $(".filter_nama_pasien_booking").val(),
      id_dokter: $(".filter_dokter_booking").val(),
      id_penjamin: $(".filter_penjamin_booking").val(),
    });
  });
  // end:: aksi filter periode transaksi

  // begin:: aksi filter nama pasien
  $(".filter_nama_pasien_booking").on("keydown", function () {
    delayTyping(function () {
      table_booking.setData(url_booking, {
        periode_registrasi: $(".filter_periode_booking").val(),
        nama_pasien: $(".filter_nama_pasien_booking").val(),
        id_dokter: $(".filter_dokter_booking").val(),
        id_penjamin: $(".filter_penjamin_booking").val(),
      });
    }, 500);
  });
  // end:: aksi filter nama pasien

  //begin:: aksi filter dokter
  $(".filter_dokter_booking").on("change", function () {
    table_booking.setData(url_booking, {
      periode_registrasi: $(".filter_periode_booking").val(),
      nama_pasien: $(".filter_nama_pasien_booking").val(),
      id_dokter: $(".filter_dokter_booking").val(),
      id_penjamin: $(".filter_penjamin_booking").val(),
    });
  });
  //end:: aksi filter dokter

  //begin:: aksi filter penjamin
  $(".filter_penjamin_booking").on("change", function () {
    table_booking.setData(url_booking, {
      periode_registrasi: $(".filter_periode_booking").val(),
      nama_pasien: $(".filter_nama_pasien_booking").val(),
      id_dokter: $(".filter_dokter_booking").val(),
      id_penjamin: $(".filter_penjamin_booking").val(),
    });
  });
  //end:: aksi filter penjamin

  // begin:: table pasien
  var btnPilihAksiBooking = function (cell, formatterParams, onRendered) {
    return `<button class="btn btn-xs btn-soft-success btn_konfirmasi_booking" type="button" data-id_pasien="${
      cell.getData().id
    }">
                Konfirmasi
            </button>`;
  };

  table_booking = new Tabulator("#table_booking", {
    layout: "fitColumns",
    placeholder: "Belum ada data perjanjian.",
    pagination: true, //enable pagination
    paginationSize: 10,
    paginationMode: "remote", //enable remote pagination
    ajaxURL: url_booking, //set url for ajax request
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
        formatter: btnPilihAksiBooking,
        hozAlign: "center",
        headerHozAlign: "center",
        headerSort: false,
      },
    ],
  });
  // end:: table pasien
});
