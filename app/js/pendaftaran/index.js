var table_pasien;
var table_rawat_jalan;
var table_rawat_inap;
var table_otc;
var data_keluarga = [];

$(document).ready(function () {
  fillDropdownPenjamin();

  // begin:: range flatpickr
  $(".flatpickr_range").flatpickr({ mode: "range", dateFormat: "d/m/Y" });
  // end:: range flatpickr

  // begin:: tambah pasien action
  $("body").on("click", ".btn-tambah-pasien", function () {
    modalExtraLarge(
      $("#url_tambah_pasien").data("url"),
      {},
      "Pasien Baru",
      "GET"
    );
  });
  // end:: tambah pasien action

  // begin:: tambah transaksi bebas action
  $("body").on("click", ".btn-transaksi-bebas", function () {
    modalDefault(
      $("#url_tambah_transaksi_bebas").data("url"),
      {},
      "Pembelian Obat Bebas",
      "GET"
    );
  });
  // end:: tambah transaksi bebas action

  // begin:: registrasi rawat jalan
  $("body").on("click", ".aksi_daftar_walkin", function () {
    modalLarge(
      $("#url_daftar_rawat_jalan").data("url"),
      { id_pasien: $(this).data("id") },
      "PENDAFTARAN RAWAT JALAN"
    );
  });
  // end:: registrasi rawat jalan

  // begin:: daftar booking online
  $("body").on("click", ".aksi_daftar_booking", function () {
    modalLarge(
      $("#url_daftar_booking").data("url"),
      { id_pasien: $(this).data("id") },
      "PENDAFTARAN BOOKING ONLINE"
    );
  });
  // end:: daftar booking online
});

function fillDropdownPenjamin() {
  $.ajax({
    url: $("#url_data_penjamin").data("url"),
    type: "GET",
    beforeSend: function () {},
    success: function (response) {
      var dropdown = $(".select_penjamin");
      $.each(response, function () {
        dropdown.append($("<option />").val(this.value).text(this.text));
      });
    },
    error: function (jqXHR, exception) {
      console.log(jqXHR);
    },
  });
}
