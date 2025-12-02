$(function () {
  $("body").on("change", "#bookingmodel-id_penjamin", function () {
    if ($(this).val() !== "")
      $.ajax({
        url: $("#url_cari_pasien_penjamin").data("url"),
        data: {
          id_pasien: $("#bookingmodel-id_pasien").val(),
          id_penjamin: $(this).val(),
        },
        type: "POST",
        beforeSend: function () {},
        success: function (response) {
          if (response.butuh_nomor) {
            if (response.nomor) {
              $("#bookingmodel-no_penjamin").val(response.nomor);
            }

            $(".div_input_no_penjamin").show();
          } else {
            $("#bookingmodel-no_penjamin").val("");
            $(".div_input_no_penjamin").hide();
          }
        },
        error: function (jqXHR, exception) {
          console.log(jqXHR);
          Swal.fire({
            title: "Oops!",
            text: "Gagal mendapatkan data penjamin!",
            icon: "error",
          });
        },
      });
  });
  // button submit data
  $("body").on("click", ".btn_submit_booking", function () {
    submitDataBooking();
  });
});

function submitDataBooking() {
  var formData = $("#form_booking").serializeArray();

  $.ajax({
    url: $("#url_submit_booking").data("url"),
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
}
