$(function () {
  // button submit data
  $("body").on("click", ".btn-submit-data", function () {
    submitDataOtc($(this).closest("form"));
  });
});

function submitDataOtc(form) {
  var formData = form.serializeArray();

  $.ajax({
    url: $("#url_submit_data").data("url"),
    data: formData,
    type: "POST",
    beforeSend: function () {
      $(".modal-spinner").show();
    },
    success: function (response) {
      $(".modal-spinner").hide();
      hideModal("#modal_df");

      table_otc.setData();

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
