// Modal Default
function modalDefault(url, param, caption, tipe = "post", maxWidth = 0) {
  $.ajax({
    url: url,
    data: param,
    type: tipe,
    beforeSend: function () {
      $(".page-spinner").show();
    },
    success: function (html) {
      $(".page-spinner").hide();
      if (maxWidth) {
        $(".modal").css("max-width", `${maxWidth}px`);
      }
      $("#modal_df .modal-title").text(caption);
      $("#modal_df .modal-body").html(html);
      $("#modal_df").modal("toggle");
    },
    error: function (jqXHR, exception) {
      $(".page-spinner").hide();
      handleError(jqXHR, exception);
    },
  });
}

// Modal Small
function modalSmall(url, param, caption, tipe = "post", maxWidth = 0) {
  $.ajax({
    url: url,
    data: param,
    type: tipe,
    beforeSend: function () {
      $(".page-spinner").show();
    },
    success: function (html) {
      $(".page-spinner").hide();
      if (maxWidth) {
        $(".modal-sm").css("max-width", `${maxWidth}px`);
      }
      $("#modal_sm .modal-title").text(caption);
      $("#modal_sm .modal-body").html(html);
      $("#modal_sm").modal("toggle");
    },
    error: function (jqXHR, exception) {
      $(".page-spinner").hide();
      handleError(jqXHR, exception);
    },
  });
}

// Modal Large
function modalLarge(url, param, caption, tipe = "post", maxWidth = 0) {
 
  $.ajax({
    url: url,
    data: param,
    type: tipe,
    beforeSend: function () {},
    success: function (html) {
      if (maxWidth) {
        $(".modal-lg").css("max-width", `${maxWidth}px`);
      }
      $("#modal_lg .modal-title").text(caption);
      $("#modal_lg .modal-body").html(html);
      showModal("#modal_lg");
    },
    error: function (jqXHR, exception) {
      handleError(jqXHR, exception);
    },
  });
}

// Modal extra Large
function modalExtraLarge(url, param, caption, tipe = "post", maxWidth = 0) {
  $.ajax({
    url: url,
    data: param,
    type: tipe,
    beforeSend: function () {
      $(".page-spinner").show();
    },
    success: function (html) {
      $(".page-spinner").hide();
      if (maxWidth) {
        $(".modal-xl").css("max-width", `${maxWidth}px`);
      }
      $("#modal_xl .modal-title").text(caption);
      $("#modal_xl .modal-body").html(html);
      showModal("#modal_xl");
    },
    error: function (jqXHR, exception) {
      handleError(jqXHR, exception);
    },
  });
}

function hideModal(element_id) {
  const modal_id = document.querySelector(element_id);
  const modal = bootstrap.Modal.getInstance(modal_id);
  modal.hide();
}

function showModal(element_id) {
  bootstrap.Modal.getOrCreateInstance(element_id, {
    backdrop: "static",
  }).show();
}
