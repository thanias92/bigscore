var data_keluarga = [];

$(document).ready(function () {
  // bersihkan data keluarga ketika modal muncul
  const modalxl = document.getElementById("modal_xl");
  modalxl.addEventListener("show.bs.modal", () => {
    data_keluarga = [];
  });

  // button tambah keluarga
  $("body")
    .off("click", ".btn_tambah_keluarga")
    .on("click", ".btn_tambah_keluarga", function(e) {
      if ($("#nama_keluarga").val() == "") {
        Swal.fire({
          title: "Data tidak valid!",
          text: "Nama keluarga harus diisi!",
          icon: "error"
        });

        return false;
      } else if ($("#alamat_keluarga").val() == "") {
        Swal.fire({
          title: "Data tidak valid!",
          text: "Alamat keluarga harus diisi!",
          icon: "error"
        });
        return false;
      } else if ($("#hubungan :selected").val() == "Pilih") {
        Swal.fire({
          title: "Data tidak valid!",
          text: "Hubungan keluarga harus dipilih!",
          icon: "error"
        });
        return false;
      } else {
        console.log($("#hubungan :selected").val());
        data_keluarga.push({
          nama_keluarga: $("#nama_keluarga").val(),
          jenis_kelamin_keluarga: $("#jenis_kelamin_keluarga").val(),
          no_hp_keluarga: $("#no_hp_keluarga").val(),
          alamat_keluarga: $("#alamat_keluarga").val(),
          hubungan: $("#hubungan :selected").text(),
          id_hubungan_keluarga: $("#hubungan :selected").val()
        });
        cardKeluarga(data_keluarga);
        return false;
      }
    });

  // button hapus data keluarga
  $("body").on("click", ".hapus_keluarga", function () {
    var data_for_delete = $(this).data("index");
    $.each(data_keluarga, function (indeks, objek) {
      data_keluarga = $.grep(data_keluarga, function (e) {
        return e.nama_keluarga != data_for_delete;
      });
    });

    cardKeluarga(data_keluarga);
  });

  // button submit data
  $("body").on("click", ".btn-submit-data", function () {
    submitDataPasien($(this).closest("form"), data_keluarga);
  });

  // default jika data keluarga masih kosong
  if (!data_keluarga) {
    $("#cards_keluarga_pasien").text("Belum ada data.");
  }
});

function cardKeluarga(data) {
  var cards = "";
  var svg =
    '<svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path opacity="0.4" d="M21.101 9.58786H19.8979V8.41162C19.8979 7.90945 19.4952 7.5 18.999 7.5C18.5038 7.5 18.1 7.90945 18.1 8.41162V9.58786H16.899C16.4027 9.58786 16 9.99731 16 10.4995C16 11.0016 16.4027 11.4111 16.899 11.4111H18.1V12.5884C18.1 13.0906 18.5038 13.5 18.999 13.5C19.4952 13.5 19.8979 13.0906 19.8979 12.5884V11.4111H21.101C21.5962 11.4111 22 11.0016 22 10.4995C22 9.99731 21.5962 9.58786 21.101 9.58786Z" fill="currentColor"></path><path d="M9.5 15.0156C5.45422 15.0156 2 15.6625 2 18.2467C2 20.83 5.4332 21.5001 9.5 21.5001C13.5448 21.5001 17 20.8533 17 18.269C17 15.6848 13.5668 15.0156 9.5 15.0156Z" fill="currentColor"></path><path opacity="0.4" d="M9.50023 12.5542C12.2548 12.5542 14.4629 10.3177 14.4629 7.52761C14.4629 4.73754 12.2548 2.5 9.50023 2.5C6.74566 2.5 4.5376 4.73754 4.5376 7.52761C4.5376 10.3177 6.74566 12.5542 9.50023 12.5542Z" fill="currentColor"></path></svg>';

  $.each(data, function (key, value) {
    cards +=
      '<div class="alert alert-primary alert-dismissible fade show" role="alert"><h4 class="alert-heading">' +
      svg +
      " " +
      value.nama_keluarga +
      "</h4> <div>Jenis Kelamin: " +
      value.jenis_kelamin_keluarga +
      "</div> <div>Hubungan: " +
      value.hubungan +
      '</div> <button type="button" class="btn-close hapus_keluarga" aria-label="Hapus" data-index=' +
      value.nama_keluarga +
      "></button> </div>";
  });

  $("#cards_keluarga_pasien").html(cards);
}

function submitDataPasien(form, dataKeluarga) {
  var formData = form.serializeArray();
  formData.push({ name: "data_keluarga", value: JSON.stringify(dataKeluarga) });

  $.ajax({
    url: $("#url_submit_data").data("url"),
    data: formData,
    type: "POST",
    beforeSend: function () {
      $(".modal-spinner").show();
    },
    success: function (response) {
      $(".modal-spinner").hide();
      hideModal("#modal_xl");
      table_pasien.setData();

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
}
