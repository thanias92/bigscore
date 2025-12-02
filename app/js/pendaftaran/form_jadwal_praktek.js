$(function () {
  // Inputmask({
  //   mask: "99:99",
  // }).mask(".timepicker");

  // $(".timepicker").flatpickr({
  //   enableTime: true,
  //   noCalendar: true,
  //   dateFormat: "H:i",
  // });

  var table_hari = new Tabulator("#tabel_hari", {
    layout: "fitColumns",
    placeholder: "Belum ada data.",
    ajaxURL: $("#url_data_hari_jadwal").data("url"), //set url for ajax request
    columns: [
      {
        title: "Hari",
        field: "hari",
        headerSort: false,
      },
      {
        title: "Jam Mulai",
        field: "jam_mulai",
        headerSort: false,
        editor: "input",
        editorParams: {
          mask: "99:99",
          maskAutoFill: true,
        },
      },
      {
        title: "Jam Selesai",
        field: "jam_selesai",
        headerSort: false,
        editor: "input",
        editorParams: {
          mask: "99:99",
          maskAutoFill: true,
        },
      },
      {
        title: "Lama Layanan (Opsional)",
        field: "lama_layanan",
        headerSort: false,
        editor: "input",
        editorParams: {
          mask: "999",
          maskAutoFill: true,
        },
      },
    ],
  });

  $("body").on("change", "#jadwalpraktekmodel-id_departemen", function () {
    if ($("#jadwalpraktekmodel-id_pegawai :selected").val() !== "") {
      console.log("id_departemen");
      table_hari.setData($("#url_data_hari_jadwal").data("url"), {
        id_departemen: $(this).val(),
        id_pegawai: $("#jadwalpraktekmodel-id_pegawai").val(),
      });
    }
  });

  $("body").on("change", "#jadwalpraktekmodel-id_pegawai", function () {
    if ($("#jadwalpraktekmodel-id_departemen :selected").val() !== "") {
      table_hari.setData($("#url_data_hari_jadwal").data("url"), {
        id_departemen: $("#jadwalpraktekmodel-id_departemen").val(),
        id_pegawai: $(this).val(),
      });
      console.log("id_pegawai");
    }
  });

  $("body").on("click", ".btn_submit_jadwal", function () {
    if ($("#jadwalpraktekmodel-id_departemen :selected").val() == "") {
      Swal.fire({
        title: "Oops!",
        text: "Poliklinik harus dipilih!",
        icon: "error",
      });

      return false;
    }

    if ($("#jadwalpraktekmodel-id_pegawai :selected").val() == "") {
      Swal.fire({
        title: "Oops!",
        text: "Dokter harus dipilih!",
        icon: "error",
      });

      return false;
    }

    var formData = $("#form_jadwal_praktek").serializeArray();
    formData.push({
      name: "tabel",
      value: JSON.stringify(table_hari.getData()),
    });

    $.ajax({
      url: $("#url_submit_jadwal").data("url"),
      data: formData,
      type: "POST",
      beforeSend: function () {
        $(".modal-spinner").show();
      },
      success: function (response) {
        $(".modal-spinner").hide();

        hideModal("#modal_lg");

        iziToast.success({
          title: "Sukses!",
          message: "Data berhasil disimpan.",
        });
      },
      error: function (jqXHR, exception) {
        $(".modal-spinner").hide();
        handleError(jqXHR, exception);
      },
    });
  });
});
