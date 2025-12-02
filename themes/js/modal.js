$(function () {
  $(document).on("click", ".showModalButton", function () {
    const dismiss =
      '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
      const loader = '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';

      //if modal isn't open; open it and load content
      $("#modal")
        .modal("show")
        .find("#modalContent")
        .empty()
        .html(loader)
        .load($(this).attr("value"), function (res, status, xhr) {
        if (status == "error") {
          Swal.fire("Proses Gagal", res, "error");
          $("#modal").modal("hide");
        }
        $(this).html(res);
      });
      //dynamiclly set the header for the modal
      document.getElementById("modalHeader").innerHTML =
        "<h5 class=\"modal-title\"><b>" + $(this).attr("title") + "</b></h5>" + dismiss;

  });
});

function onModal(title,url) {
  const dismiss =
    '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
  const loader =
    '<div class="d-flex justify-content-center bd-highlight mb-3"><div class="lds-dual-ring"><div></div><div></div></div></div>';

    $("#modal")
      .modal("show")
      .find("#modalContent")
      .empty()
      .html(loader)
      .load(url, function(res, status, xhr) {
        if (status == "error") {
          $("#modal").modal("hide");
          Swal.fire("Proses Gagal", res, "error");
        }
        $(this).html(res);
      });
    $("#modalHeader").html(
      '<h5 class="modal-title"><b>' + title + "</b></h5>" + dismiss
    );
}
