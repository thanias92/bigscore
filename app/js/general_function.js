function handleError(jqXHR, exception) {
  var msgerror = "";
  if (jqXHR.status === 0) {
    msgerror = "Koneksi jaringan bermasalah.";
  } else if (jqXHR.status == 404) {
    msgerror = "Halaman tidak ditemukan. [404]";
  } else if (jqXHR.status == 500) {
    msgerror = "Internal Server Error [500].";
  } else if (exception === "parsererror") {
    msgerror = "Requested JSON parse gagal.";
  } else if (exception === "timeout") {
    msgerror = "Request Time Out.";
  } else if (exception === "abort") {
    msgerror = "Gagal request ajax.";
  } else {
    msgerror = "Error.\n" + jqXHR.responseText;
  }
  Swal.fire({
    title: "Kesalahan Sistem!",
    html: msgerror + ", coba ulangi kembali !",
    type: "error",

    buttonsStyling: false,

    confirmButtonText: "OK",
    confirmButtonClass: "btn btn-default"
  });
}

var delayTyping = (function() {
  var timer = 0;
  return function(callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();

function addSpinner(classadd, method) {
  if (method == "after") {
    $(classadd).after(
      '&nbsp<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
    );
  } else if (method == "append") {
    $(classadd).prop("disabled", true);
    $(classadd).append(
      '&nbsp<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
    );
  }
}

function uangkoma(val) {
  var konversi = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
  return konversi;
}

function toMoney(num) {
  return num
    .replace(/\D/g, "")
    .replace(/([0-9])([0-9]{})$/, "$1.$2")
    .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
}

function formatCurrency(num) {
  num = parseFloat(num);
  num = num.toString().replace(/\$|\,/g, "");
  if (isNaN(num)) num = "0";
  sign = num == (num = Math.abs(num));
  num = Math.floor(num * 100 + 0.50000000001);
  cents = num % 100;
  num = Math.floor(num / 100).toString();
  if (cents < 10) cents = "0" + cents;
  for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
    num =
      num.substring(0, num.length - (4 * i + 3)) +
      "." +
      num.substring(num.length - (4 * i + 3));
  return (sign ? "" : "-") + num;
}

function formatUang(classid) {
  var num = toMoney($("#" + classid).val());
  $("#" + classid).val(num);
}

function toNormalFormat(num) {
  var result = num.toString().replace(/[\. ,:-]+/g, "");
  var tonumber = parseFloat(result);
  if (isNaN(tonumber)) {
    return 0;
  } else {
    return tonumber;
  }
}

function toMoneyKoma(num) {
  return num
    .replace(/\D/g, "")
    .replace(/([0-9])([0-9]{})$/, "$1.$2")
    .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ",");
}

function tonumber(val) {
  val = val.toString();
  if (isNaN(val)) {
    return 0;
  } else {
    var number = Number(val.replace(/[^0-9.-]+/g, ""));
    return parseFloat(number);
  }
}

function roundNominal(numberInt) {
  let getNumber = String(numberInt);
  getNumber = getNumber.split(".");
  let output = numberInt;
  if (getNumber.length > 1) {
    let fixNumber = getNumber[1];

    let checkSplit = fixNumber.split("");
    if (checkSplit.length > 5) {
      output = Math.ceil(numberInt);
    } else {
      output = parseInt(getNumber[0]);
    }
  }
  return output;
}

function formatBirthDate(date) {
  convertMonth = month => {
    switch (month) {
      case "01":
        return "Januari";
        break;
      case "02":
        return "Februari";
        break;
      case "03":
        return "Maret";
        break;
      case "04":
        return "April";
        break;
      case "05":
        return "Mei";
        break;
      case "06":
        return "Juni";
        break;
      case "07":
        return "Juli";
        break;
      case "08":
        return "Agustus";
        break;
      case "09":
        return "September";
        break;
      case "10":
        return "Oktober";
        break;
      case "11":
        return "November";
        break;
      case "12":
        return "Desember";
        break;
      default:
        return "Januari";
        break;
    }
  };

  let dataDate = date.split("/");
  let convertDate = convertMonth(dataDate[1]);
  let fixingDate = dataDate[0] + " " + convertDate + " " + dataDate[2];
  return fixingDate;
}

function calculateAge(birthDate) {
  var a = moment();
  var b = moment(birthDate, "DD/MM/YYYY");
  var calcAge = moment.duration(a.diff(b));
  var years = calcAge.years();
  var months = calcAge.months();
  var days = calcAge.days();
  var age = `${years} Tahun ${months} Bulan ${days} Hari`;
  return age;
}

function timeRegistration(inputDate) {
  let getTime = moment(inputDate).format("DD MMM YYYY");
  return getTime;
}
