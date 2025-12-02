<?php

namespace app\components;
use DateTime;
use Yii;
use yii\db\Query;
use app\models\ItemTransaksi;
use app\models\JenisItemTransaksi;
use app\models\ResepPasien;
use app\models\TagihanPasien;
use app\models\PembayaranJasaMedis;
use app\models\SysConfig;



class Lib
{

    public $select;
    public $from;
    public $join;
    public $where;
    public $group;
    public $order;

    public static function getUmur($tgllahir)
    {
        $datetime1 = new \DateTime($tgllahir);
        $datetime2 = new \DateTime('now', new \DateTimeZone('UTC'));
        $diff = $datetime1->diff($datetime2);
        return $diff->y;

    }

    public static function bulanLamaBekerja($tgllahir)
    {
        $datetime1 = new \DateTime($tgllahir);
        $datetime2 = new \DateTime('now', new \DateTimeZone('UTC'));
        $diff = $datetime1->diff($datetime2);
        return $diff;

    }

    public static function getUmurDetail($tgllahir)
    {
        $awal = date_create($tgllahir);
        $akhir = date_create(); // waktu sekarang
        $diff = date_diff($awal, $akhir);

        return $diff->y . ' tahun ' . $diff->m . ' bulan ' . $diff->d . ' hari';
    }
    public static function getUmurTahunBulan($tgllahir)
    {
        $awal = date_create($tgllahir);
        $akhir = date_create(); // waktu sekarang
        $diff = date_diff($awal, $akhir);

        return $diff->y . ' tahun ' . $diff->m . ' bulan ';
    }

    public static function getUmurDetailEng($tgllahir)
    {
        $awal = date_create($tgllahir);
        $akhir = date_create(); // waktu sekarang
        $diff = date_diff($awal, $akhir);

        return $diff->y . ' year ' . $diff->m . ' month ' . $diff->d . ' day';
    }

    public static function getLamaDirawatFarmasi($tgllahir)
    {
        $awal = date_create($tgllahir);
        $akhir = date_create(); // waktu sekarang
        $diff = date_diff($awal, $akhir);

        return $diff->m . ' bulan ' . $diff->d . ' hari';
    }

    public static function getUmurHari($tgllahir)
    {
        $awal = date_create($tgllahir);
        $akhir = date_create(); // waktu sekarang
        $diff = date_diff($awal, $akhir);

        return $diff->d . ' hari';
    }

    public static function getUmurBulan($tgllahir)
    {
        $awal = date_create($tgllahir);
        $akhir = date_create(); // waktu sekarang
        $diff = date_diff($awal, $akhir);

        return $diff->m;
    }

    public static function getUmurDetailBy($tgllahir)
    {

        $datetime1 = new DateTime($tgllahir);
        $datetime2 = new DateTime(date('Y-m-d'));
        $difference = $datetime1->diff($datetime2);


        return $difference->days . ' hari';
    }

    public static function getUmurLab($tgllahir)
    {

        $datetime1 = new DateTime($tgllahir);
        $datetime2 = new DateTime(date('Y-m-d'));
        $difference = $datetime1->diff($datetime2);


        return $difference->days;
    }

    public function querymarger($data)
    {
        $sql = '';
        $sql .= $data->select . ' ';
        $sql .= $data->from . ' ';
        $sql .= $data->join . ' ';
        $sql .= 'WHERE ' . $data->where . ' ';
        $sql .= $data->group . ' ';
        $sql .= $data->order . ' ';

        return $sql;
    }


    public static function getNamaHari($date)
    {
        $namahari = date('D', strtotime($date));
        //Function date(String1, strtotime(String2)); adalah fungsi untuk mendapatkan nama hari
        return Lib::getHari($namahari);
    }


    public static function getHari($hari)
    {
        switch ($hari) {
            case 'Mon':
                return "Senin";
                break;
            case 'Tue':
                return "Selasa";
                break;
            case 'Wed':
                return "Rabu";
                break;
            case 'Thu':
                return "Kamis";
                break;
            case 'Fri':
                return "Jumat";
                break;
            case 'Sat':
                return "Sabtu";
                break;
            case 'Sun':
                return "Minggu";
                break;
        }
    }

    public static function getBulanangka($bulan)
    {
        switch ($bulan) {
            case 'Jan':
                return '01';
                break;
            case 'Feb':
                return '02';
                break;
            case 'Mar':
                return '03';
                break;
            case 'Apr':
                return '04';
                break;
            case 'May':
                return '05';
                break;
            case 'Mey':
                return '05';
                break;
            case 'Jun':
                return '06';
                break;
            case 'Jul':
                return '07';
                break;
            case 'Aug':
                return '08';
                break;
            case 'Sep':
                return '09';
                break;
            case 'Oct':
                return '10';
                break;
            case 'Nov':
                return '11';
                break;
            case 'Dec':
                return '12';
                break;
        }
    }

    public static function getBulan($bln)
    {
        switch ($bln) {
            case 1:
                return "Januari";
                break;
            case 2:
                return "Februari";
                break;
            case 3:
                return "Maret";
                break;
            case 4:
                return "April";
                break;
            case 5:
                return "Mei";
                break;
            case 6:
                return "Juni";
                break;
            case 7:
                return "Juli";
                break;
            case 8:
                return "Agustus";
                break;
            case 9:
                return "September";
                break;
            case 10:
                return "Oktober";
                break;
            case 11:
                return "November";
                break;
            case 12:
                return "Desember";
                break;
        }
    }

    public static function getBulansingkat($bln)
    {
        switch ($bln) {
            case 1:
                return "Jan";
                break;
            case 2:
                return "Feb";
                break;
            case 3:
                return "Mar";
                break;
            case 4:
                return "Apr";
                break;
            case 5:
                return "May";
                break;
            case 6:
                return "Jun";
                break;
            case 7:
                return "Jul";
                break;
            case 8:
                return "Aug";
                break;
            case 9:
                return "Sep";
                break;
            case 10:
                return "Oct";
                break;
            case 11:
                return "Nov";
                break;
            case 12:
                return "Dec";
                break;
        }
    }

    public static function jenisKelamin($jk)
    {
        if ($jk == 'L')
            return 'Laki-laki';
        else
            return 'Perempuan';
    }

    public static function jenisKelaminInEnglish($jk)
    {
        if (strtolower($jk) == 'laki-laki')
            return 'Male';
        else
            return 'Female';
    }

    public static function shortcutJenisKelamin($jk)
    {
        if ($jk == 'Laki-laki')
            return 'L';
        else
            return 'P';
    }

    public static function tempelKanan($str, $tempel)
    {
        return $str . " " . $tempel;
    }

    public static function yaTidak($num)
    {
        if ($num == 1)
            return 'Ya';
        else
            return 'Tidak';
    }

    public static function sudahBelum($num)
    {
        if ($num == 1)
            return 'Sudah';
        else
            return 'Belum';
    }

    public static function uang($rp)
    {
        return 'Rp ' . $rp;
    }

    public static function kosong($str)
    {
        if (empty($str))
            return '-';
        else
            return $str;
    }

    public static function tglkosong($str)
    {
        if ($str == '0000-00-00 00:00:00')
            return '-';
        else
            return $str;
    }

    public static function ketPost($num)
    {
        switch ($num) {
            case 1:
                return "Publish";
                break;
            default:
                return "Draft";
                break;
        }
    }


    public static function hitungHari($tgl1, $tgl2)
    {
        $startTimeStamp = strtotime($tgl1);
        $endTimeStamp = strtotime($tgl2);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / (24 * 60 * 60);
        $numberDays = intval($numberDays);
        return $numberDays;
    }

    public static function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }
        $IP = $_SERVER['REMOTE_ADDR'];

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'IP' => $IP,
        );
    }


    public static function getWaktu($ex, $setting)
    {
        $pecah = explode(" ", $ex);
        if ($setting === 'jam') {
            return $pecah[1];
        } elseif ($setting === 'tanggal') {
            return substr($pecah[0], 8, 11);
        } elseif ($setting === 'bulan') {
            return substr($pecah[0], 5, -3);
        } elseif ($setting === 'tahun') {
            return substr($pecah[0], 0, 4);
        }

    }

    public static function Waktu($date, $nama_hari=false){
        if(isset($date)) {
  
          $hari_ini = date('D', strtotime($date));
                      
          if($hari_ini == 'Sun') {
              $hari = 'Minggu';
          } elseif($hari_ini == 'Mon') {
              $hari = 'Senin';
          } elseif($hari_ini == 'Tue') {
              $hari = 'Selasa';
          } elseif($hari_ini == 'Wed') {
              $hari = 'Rabu';
          } elseif($hari_ini == 'Thu') {
              $hari = 'Kamis';
          } elseif($hari_ini == 'Fri') {
              $hari = 'Jumat';
          } else {
              $hari = 'Sabtu';
          }
  
          $data = date('d/m/Y, H:i:s', strtotime($date));
  
          if($nama_hari == true) {
              return $hari.', '.$data;
          } else {
              return $data;
          }
          
        }
        return null;
      }

    public static function cut_text($x, $n)
    {
        $kata = strtok(strip_tags($x), " ");
        $new = "";
        for ($i = 1; $i <= $n; $i++) {    //membatasi berapa kata yang akan ditampilkan di halaman utama
            $new .= $kata;        //tulis isi agenda
            $new .= " ";
            $kata = strtok(" ");
        }
        return $new;
    }


    public static function cek_img_tag($text, $original)
    {
        //membuat auto thumbnails
        @preg_match("/src=\"(.+)\"/", $text, $cocok);
        @$patern = explode("\"", $cocok[1]);
        $img = str_replace("\"/>", "", $patern[0]);
        $img = str_replace("../", "", $img);
        $img = str_replace("/>", "", $img);
        if ($img == "") {
            $img = $original;
        } else {
            $img = str_replace("\&quot;", "", $img);

        }

        return $img;
    }

    public static function simbolRemoving($title)
    {
        $linkbaru = strtolower($title);
        $tanda = array("|", ",", "\"", "'", ".", "(", ")", "-", "_", ":", ";", "?", "!", "@", "#", "\$", "%", "^", "&", "*", "+", "/", "\\", ">", "<", "\r", "\t", "\n");
        $rep = stripslashes(str_replace($tanda, "", $linkbaru));
        $rep = stripslashes(str_replace('  ', "-", $rep));
        $rep = stripslashes(str_replace(' ', "-", $rep));
        return $rep;
    }

    public static function selisih2tanggal($tgl1, $tgl2)
    {
        $pecah1 = explode("-", $tgl1);
        $date1 = $pecah1[2];
        $month1 = $pecah1[1];
        $year1 = $pecah1[0];

        // memecah tanggal untuk mendapatkan bagian tanggal, bulan dan tahun
        // dari tanggal kedua

        $pecah2 = explode("-", $tgl2);
        $date2 = $pecah2[2];
        $month2 = $pecah2[1];
        $year2 = $pecah2[0];

        // menghitung JDN dari masing-masing tanggal

        $jd1 = GregorianToJD($month1, $date1, $year1);
        $jd2 = GregorianToJD($month2, $date2, $year2);

        // hitung selisih hari kedua tanggal

        $selisih = $jd2 - $jd1;

        return $selisih;
    }

    public static function tglIndo($ex, $type, $day = true)
    {

        if ($ex == NULL) return '-';
        if ($ex == '0000-00-00') return '-';
        if ($ex == '0000-00-00') return '-';
        if ($ex == '0000-00-00 00:00:00') return '-';

        if ($type == '00-bulan-0000') {
            if ($day == true) {
                $pecah = explode(" ", $ex);
                $tglsaja = $pecah[0];
            } else {
                $tglsaja = $ex;
            }
            $pecahtanggal = explode("-", $tglsaja);
            $bulan = Lib::getBulan(ltrim($pecahtanggal[1], '0'));

            return $pecahtanggal[2] . ' ' . $bulan . ' ' . $pecahtanggal[0];
        } else
            if ($type == 'bulan') {
                if ($day == true) {
                    $pecah = explode(" ", $ex);
                    $tglsaja = $pecah[0];
                } else {
                    $tglsaja = $ex;
                }
                $pecahtanggal = explode("-", $tglsaja);
                $bulan = Lib::getBulan(ltrim($pecahtanggal[1], '0'));

                return $bulan;
            } else if ($type == '00-bul-0000') {
                $pecah = explode(" ", $ex);

                $tglsaja = $pecah[0];
                $pecahtanggal = explode("-", $tglsaja);
                $bulan = Lib::getBulansingkat(ltrim($pecahtanggal[1], '0'));

                return $pecahtanggal[2] . '-' . $bulan . '-' . $pecahtanggal[0];
            } else if ($type == '00 bul 0000') {
                $pecah = explode(" ", $ex);

                $tglsaja = $pecah[0];
                $pecahtanggal = explode("-", $tglsaja);
                $bulan = Lib::getBulansingkat(ltrim($pecahtanggal[1], '0'));

                return $pecahtanggal[2] . ' ' . $bulan . ' ' . $pecahtanggal[0];
            } else if ($type == '00-00-0000') {
                $pecah = explode(" ", $ex);
                $tglsaja = $pecah[0];
                $pecahtanggal = explode("-", $tglsaja);
                $bulan = $pecahtanggal[1];

                return $pecahtanggal[2] . '-' . $bulan . '-' . $pecahtanggal[0];
            } else if ($type == 'Hari 00-BulanSingkat-0000 WIB') {
                $pecah = explode(" ", $ex);
                $nameofDay = '';
                if ($day == true) {
                    $nameofDay = Lib::getNamaHari($pecah[0]) . ', ';
                } else {
                    $nameofDay = '';
                }
                if (!empty($pecah[1])) {
                    $tgl = $pecah[0];
                    $tanggal = substr($tgl, 8, 2);
                    $bulan = Lib::getBulan(substr($tgl, 5, 2));
                    $tahun = substr($tgl, 0, 4);
                    return $nameofDay . $tanggal . ' ' . substr($bulan, 0, 3) . ' ' . $tahun . ' ' . $pecah[1] . ' WIB';
                }
            
            } else if ($type == 'Hari 00-Bulan-0000') {
                $pecah = explode(" ", $ex);
                $nameofDay = '';
                if ($day == true) {
                    $nameofDay = Lib::getNamaHari($pecah[0]) . ', ';
                } else {
                    $nameofDay = '';
                }
                if (!empty($pecah[1])) {
                    $tgl = $pecah[0];
                    $tanggal = substr($tgl, 8, 2);
                    $bulan = Lib::getBulan(substr($tgl, 5, 2));
                    $tahun = substr($tgl, 0, 4);
                    return $nameofDay . $tanggal . ' ' . substr($bulan, 0, 11). ' ' . $tahun ;
                }
            } else if ($type == '00-BulanSingkat-0000 WIB') {
                $pecah = explode(" ", $ex);
                $nameofDay = '';
                if ($day == true) {
                    $nameofDay = Lib::getNamaHari($pecah[0]) . ', ';
                } else {
                    $nameofDay = '';
                }
                if (!empty($pecah[1])) {
                    $tgl = $pecah[0];
                    $tanggal = substr($tgl, 8, 2);
                    $bulan = Lib::getBulan(substr($tgl, 5, 2));
                    $tahun = substr($tgl, 0, 4);
                    return $tanggal . ' ' . substr($bulan, 0, 3) . ' ' . $tahun . ' ' . $pecah[1] . ' WIB';
                }
            } else if ($type == '00-BulanSingkat-0000 WIB(wDetik)') {
                $pecah = explode(" ", $ex);
                $nameofDay = '';
                if ($day == true) {
                    $nameofDay = Lib::getNamaHari($pecah[0]) . ', ';
                } else {
                    $nameofDay = '';
                }
                if (!empty($pecah[1])) {
                    $tgl = $pecah[0];
                    $tanggal = substr($tgl, 8, 2);
                    $bulan = Lib::getBulan(substr($tgl, 5, 2));
                    $tahun = substr($tgl, 0, 4);
                    return $tanggal . ' ' . substr($bulan, 0, 3) . ' ' . $tahun . ' ' . substr($pecah[1], 0, 5) . ' WIB';
                }

            } else if ($type == '00-00-0000 (wDetik)') {
                $pecah = explode(" ", $ex);
                $nameofDay = '';
                if ($day == true) {
                    $nameofDay = Lib::getNamaHari($pecah[0]) . ', ';
                } else {
                    $nameofDay = '';
                }
                if (!empty($pecah[1])) {
                    $tgl = $pecah[0];
                    $tanggal = substr($tgl, 8, 2);
                    $bulan = substr($tgl, 5, 2);
                    $tahun = substr($tgl, 0, 4);
                    return $tanggal . '-' . $bulan . '-' . $tahun . ' ' . substr($pecah[1], 0, 5);
                }
            } else if ($type == '00-BulanLengkap-0000 WIB') {
                $pecah = explode(" ", $ex);
                $nameofDay = '';
                if ($day == true) {
                    $nameofDay = Lib::getNamaHari($pecah[0]) . ', ';
                } else {
                    $nameofDay = '';
                }
                if (!empty($pecah[1])) {
                    $tgl = $pecah[0];
                    $tanggal = substr($tgl, 8, 2);
                    $bulan = Lib::getBulan(substr($tgl, 5, 2));
                    $tahun = substr($tgl, 0, 4);
                    return $tanggal . ' ' . substr($bulan, 0, 11) . ' ' . $tahun . ' ' . $pecah[1] . ' WIB';
                }
            }
    }

    public static function tglIndoTanpaJam($ex)
    {

        if ($ex == '0000-00-00') return '-';
        if ($ex == '0000-00-00 00:00:00') return '-';;

        $pecahtanggal = explode("-", $ex);
        $bulan = Lib::getBulan(int($pecahtanggal[1]));

        return $pecahtanggal[1];
    }


    public static function rupiah($nilai, $pecahan = 0)
    {
        return number_format($nilai, $pecahan, ',', '.');
    }

    public static function uangkoma($nilai)
    {
        $ex = explode('.', $nilai);
        $val = substr(trim(strrev(chunk_split(strrev($ex[0]), 3, '.'))), 1);
        if (isset($ex[1])) {
            return $val . ',' . $ex[1];
        } else {
            return $val;
        }

    }

    public static function uangindo($nilai)
    {
        $ex = explode('.', $nilai);
        $val = substr(trim(strrev(chunk_split(strrev($ex[0]), 3, ','))), 1);
        if (isset($ex[1])) {
            return $val . '.' . $ex[1];
        } else {
            return $val;
        }

    }

    public static function uangtitik($nilai)
    {
        $val = substr(trim(strrev(chunk_split(strrev($nilai), 3, '.'))), 1);
        return $val;
    }

    public static function getNo($date, $query)
    {

        $last = Yii::$app->db->createCommand
        ($query)->bindValue(':waktu', $date)->queryAll();

        if (empty($last)) {
            return date("ymd") . sprintf("%04s", 1);
        } else {
            //return date("ymd").sprintf("%04s",$last+1);
            $integerIDs = array_map('intval', $last);
            return date("ymd") . sprintf("%04s", $integerIDs[0] + 1);
            //print_r($inc);exit();
        }
    }

    public static function getNoTagihanGen()
    {
        $last = TagihanPasien::find()
            ->where(['DATE(waktu_tagihan)' => date("Y-m-d")])
            ->count();

        if (empty($last)) {
            return date("ymd") . sprintf("%04s", 1);
        } else {
            return date("ymd") . sprintf("%04s", $last + 1);
        }
    }

    public static function getNoPembayaranJasmed()
    {
        $last = PembayaranJasaMedis::find()
            ->where(['EXTRACT(MONTH FROM waktu_pembayaran)' => date("m")])
            ->andWhere(['status_pembayaran' => 1])
            ->count();

        if (empty($last)) {
            return 'APP-' . date("ymd") . sprintf("%04s", 1);
        } else {
            return 'APP-' . date("ymd") . sprintf("%04s", $last + 1);
        }
    }


    public static function tglNormalOrig($date)
    {
        $org = explode(" ", $date);
        $pecahtanggal = explode("-", $org[0]);
        return $pecahtanggal[2] . '-' . $pecahtanggal[1] . '-' . $pecahtanggal[0] . ' ' . $org[1];
    }

    public static function tglNormal($date, $type)
    {
        $pecahtanggal = explode("-", $date);
        $bulan = Lib::getBulanangka($pecahtanggal[1]);
        if ($type == 'to') {
            $his = '23:59:59';

        } elseif ($type == 'from') {
            $his = '00:00:00';
        }
        return $pecahtanggal[2] . '-' . $bulan . '-' . $pecahtanggal[0] . ' ' . $his;
    }

    public static function tglNormalValidasi($date, $type, $hour)
    {
        $pecahtanggal = explode("-", $date);
        $bulan = Lib::getBulanangka($pecahtanggal[1]);
        if ($type == 'to') {
            $his = $hour.':59:59';

        } elseif ($type == 'from') {
            $his = $hour.':00:00';
        }
        return $pecahtanggal[2] . '-' . $bulan . '-' . $pecahtanggal[0] . ' ' . $his;
    }

    public static function tglNormalBedPasien($date)
    {
        $pecahtanggal = explode("-", $date);
        $bulan = Lib::getBulanangka($pecahtanggal[1]);
        $his = '01:00:00';
        return $pecahtanggal[2] . '-' . $bulan . '-' . $pecahtanggal[0] . ' ' . $his;
    }

    // fungsi untuk menormalkan tgl, tambahan waktu diakhir hari tsb
    public static function tglNormalAkhir($date)
    {
        $pecahtanggal = explode("-", $date);
        $bulan = Lib::getBulanangka($pecahtanggal[1]);
        $his = '23:59:59';
        return $pecahtanggal[2] . '-' . $bulan . '-' . $pecahtanggal[0] . ' ' . $his;
    }

    public static function getGenKodeItem($string)
    {
        $query = new Query;
        $query->select('kode_item')
            ->from('item')
            //->where(['like', 'kode_item', '%' . $string . '%', false])
            ->where(['regexp_replace(kode_item, \'\d\', \'\', \'g\')' => $string])
            ->orderBy(['kode_item' => SORT_DESC]);

        $command = $query->createCommand();
        $result = $command->queryScalar();
        //print_r($string);exit();
        if (empty($result)) {
            $kode['last'] = $string . sprintf("%05s", 0);
            $kode['new'] = $string . sprintf("%05s", 1);
            return $kode;
        } else {
            $karakter = preg_replace("/[^a-zA-Z]+/", "", $result);
            $jumlah_karakter = strlen($karakter);

            if ($jumlah_karakter > 3) {
                $num = substr($result, -3);
                $kode['last'] = $result;
                $kode['new'] = $string . sprintf("%03s", $num + 1);
                return $kode;
            } else {
                $num = substr($result, -4);
                $kode['last'] = $result;
                $kode['new'] = $string . sprintf("%05s", $num + 1);
                return $kode;
            }

        }
    }

    public static function generateNoTransaksi($id_jenis_item_transaksi)
    {
        $count_item_transaksi = (int)ItemTransaksi::find()->where(['DATE(waktu_transaksi)' => date("Y-m-d")])->andWhere(['id_jenis_item_transaksi' => $id_jenis_item_transaksi])->count();
        $current_year = date("Y");
        $current_year = substr($current_year, 2);
        $current_date = $current_year . date("md");
        $urutan_resep = str_pad(($count_item_transaksi + 1), 4, '0', STR_PAD_LEFT);

        $jenis_item_transaksi = JenisItemTransaksi::findOne($id_jenis_item_transaksi);

        return $jenis_item_transaksi->kode_jenis_transaksi . "-" . $current_date . $urutan_resep;
    }

    // fungsi ini hanya me return no_transakasi + no_urut (tanpa kode trx)
    public static function generateNoTransaksiOnly($id_jenis_item_transaksi)
    {
        $count_item_transaksi = ItemTransaksi::find()->where(['DATE(waktu_transaksi)' => date("Y-m-d")])->andWhere(['id_jenis_item_transaksi' => $id_jenis_item_transaksi])->count();
        $current_year = date("Y");
        $current_year = substr($current_year, 2);
        $current_date = $current_year . date("md");
        $urutan_resep = str_pad(($count_item_transaksi + 1), 4, '0', STR_PAD_LEFT);

        return $current_date . $urutan_resep;
    }

    public static function generateNoResep($id_registrasi)
    {
        $count_resep = ResepPasien::find()->where(['DATE(waktu_resep)' => date('Y-m-d')])->count();
        $current_date = date("Ymd");
        $urutan_resep = str_pad(($count_resep + 1), 4, '0', STR_PAD_LEFT);

        return "DD" . $current_date . $urutan_resep;;
    }

    public static function radiolistc($list, $form, $model, $field, $val)
    {
        $val = empty($val[$field]) ? '' : $val[$field];
        if (isset($model->$field)) {
            $val = $model->$field;
        }
        $chk = '';
        if (empty($val)) {
            $chk = $field . '0';
        } else {
            $i = 0;
            foreach ($list as $key => $a) {
                if ($val == $key or empty($val)) {
                    $chk = $field . $i;
                }
                $i++;
            }
        }

        echo '
			<script>
			$( document ).ready(function() {
				var chk = "' . $chk . '";
				$("#" + chk).prop("checked", true);
			});
			</script>
		';
        echo $form->field($model, $field)->radioList($list, [
                'item' => function ($index, $label, $name, $checked, $value) use ($field) {
                    $return = '<label class="m-radio">';
                    $return .= '<input type="radio" id="' . $field . $index . '" class="c' . $index . '" name="' . $name . '" value="' . $value . '"> ' . $label . '<span></span>';
                    $return .= '</label>';
                    return $return;
                }
            ]
        );
    }

    public static function radiolistcDyn($list, $form, $model, $field, $get, $func)
    {
        $val = empty($get[$field]) ? '' : $get[$field];

        if (empty($get)) {
            $chk = $field . '0';
        } else {
            $i = 0;
            foreach ($list as $key => $a) {
                if ($val == $key or empty($val)) {
                    $chk = $field . $i;
                }
                $i++;
            }
        }

        echo '
			<script>
			$( document ).ready(function() {
				var chk = "' . $chk . '";
				$("#" + chk).prop("checked", true);
			});
			</script>
		';
        echo $form->field($model, $field)->radioList($list, [
                'item' => function ($index, $label, $name, $checked, $value) use ($field, $func) {
                    $return = '<label class="m-radio">';
                    $return .= '<input onclick="' . $func . '" type="radio" id="' . $field . $index . '" class="c' . $index . '" name="' . $name . '" value="' . $value . '"> ' . $label . '<span></span>';
                    $return .= '</label>';
                    return $return;
                }
            ]
        );
    }

    public static function selectc($list, $form, $model, $field, $val)
    {
        if (empty($val)) {
            // echo '
            // <script>
            // $( document ).ready(function() {
            // $("#0.'.$field.'").prop("checked", true);
            // });
            // </script>
            // ';
        } else {
            echo '
				<script>
				$( document ).ready(function() {
					';
            foreach ($val as $val) {
                echo '$("input.' . $field . '[value=\"' . $val . '\"]").prop("checked", true);';
            }
            echo '
				});
				</script>
			';
        }

        echo $form->field($model, $field)->radioList($list, [
                'item' => function ($index, $label, $name, $checked, $value) use ($field) {
                    $return = '<label class="m-checkbox">';
                    $return .= '<input type="checkbox" id="' . $index . '" class="' . $field . '" name="' . $name . '[]" value="' . $value . '"> ' . $label . '<span></span>';
                    $return .= '</label>';
                    return $return;
                }
            ]
        );
    }

    public static function modalShow($modalShow)
    {
        $html = '<button type="button" class="' . $modalShow['class'] . '" data-toggle="modal" data-target="#' . $modalShow['namemodal'] . '">' . $modalShow['label'] . '</button>';

        return $html;
    }

    public static function spinner()
    {
        $html = '<div id="spinner" class="text-center" style="display:none;">
					<div class="text-center m-loader m-loader--brand m-loader--lg" style="width: 30px; display: inline-block;">
						<div style="margin:100px;"></div>
					</div>
				</div>';

        return $html;
    }

    public static function getDiscountpersen($hargaawal, $hargadiskon)
    {
        $selisih = $hargaawal - $hargadiskon;
        $var = ($selisih / $hargaawal) * 100;
        return $var;
    }

    public static function getDiscountpersen2($hargaawal, $hargadiskon)
    {
        if($hargadiskon > 0) {
            $var = ($hargadiskon * 100) / $hargaawal;
            return $var;
        } else {
            return 0;
        }
    }

    public static function getDataHari()
    {
        $haris = ['Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu', 'Kamis' => 'Kamis', 'Jumat' => 'Jumat', 'Sabtu' => 'Sabtu', 'Minggu' => 'Minggu'];
        return $haris;
    }

    public static function kekata($x)
    {
        $x = abs($x);
        $angka = array("", "satu", "dua", "tiga", "empat", "lima",
            "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($x < 12) {
            $temp = " " . $angka[$x];
        } else if ($x < 20) {
            $temp = Lib::kekata($x - 10) . " belas";
        } else if ($x < 100) {
            $temp = Lib::kekata($x / 10) . " puluh" . Lib::kekata($x % 10);
        } else if ($x < 200) {
            $temp = " seratus" . Lib::kekata($x - 100);
        } else if ($x < 1000) {
            $temp = Lib::kekata($x / 100) . " ratus" . Lib::kekata($x % 100);
        } else if ($x < 2000) {
            $temp = " seribu" . Lib::kekata($x - 1000);
        } else if ($x < 1000000) {
            $temp = Lib::kekata($x / 1000) . " ribu" . Lib::kekata($x % 1000);
        } else if ($x < 1000000000) {
            $temp = Lib::kekata($x / 1000000) . " juta" . Lib::kekata($x % 1000000);
        } else if ($x < 1000000000000) {
            $temp = Lib::kekata($x / 1000000000) . " milyar" . Lib::kekata(fmod($x, 1000000000));
        } else if ($x < 1000000000000000) {
            $temp = Lib::kekata($x / 1000000000000) . " trilyun" . Lib::kekata(fmod($x, 1000000000000));
        }
        return $temp;
    }
    public static function terbilang($x, $style = 4) {
        if ($x == 0) {
            $hasil = "nol rupiah";
        } else if ($x < 0) {
            $hasil = "minus " . trim(Lib::kekata($x));
        } else {
            $hasil = trim(Lib::kekata($x)).' rupiah';
        }
        switch ($style) {
            case 1:
                $hasil = strtoupper($hasil);
                break;
            case 2:
                $hasil = strtolower($hasil);
                break;
            case 3:
                $hasil = ucwords($hasil);
                break;
            default:
                $hasil = ucfirst($hasil);
                break;
        }
        return $hasil;
    }

    function terbilangqueue($x, $style = 4)
    {
        if ($x == 0) {
            $hasil = "nol";
        } else if ($x < 0) {
            $hasil = "minus " . trim(Lib::kekata($x));
        } else {
            $hasil = trim(Lib::kekata($x));
        }
        switch ($style) {
            case 1:
                $hasil = strtoupper($hasil);
                break;
            case 2:
                $hasil = strtolower($hasil);
                break;
            case 3:
                $hasil = ucwords($hasil);
                break;
            default:
                $hasil = ucfirst($hasil);
                break;
        }
        return $hasil;
    }

    // fungsi convert bilangan to romawi format
    public static function toRomawi($number)
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }


    public static function getGreeting()
    {
        date_default_timezone_set("Asia/Jakarta");

        $b = time();
        $hour = date("G", $b);
        $result = null;

        if ($hour >= 0 && $hour <= 11) {
            $result = "Selamat Pagi";
        } elseif ($hour >= 12 && $hour <= 14) {
            $result = "Selamat Siang";
        } elseif ($hour >= 15 && $hour <= 17) {
            $result = "Selamat Sore";
        } elseif ($hour >= 17 && $hour <= 18) {
            $result = "Selamat Petang";
        } elseif ($hour >= 19 && $hour <= 23) {
            $result = "Selamat Malam";
        }

        return $result;
    }

    public static function dateInd($ex, $day = true)
    {
        $pecah = explode(" ", $ex);
        $nameofDay = '';
        if ($day == true) {
            $nameofDay = Lib::getNamaHari($pecah[0]) . ', ';
        } else {
            $nameofDay = '';
        }
        if (!empty($pecah[1])) {
            $tgl = $pecah[0];
            $tanggal = substr($tgl, 8, 2);
            $bulan = Lib::getBulan(substr($tgl, 5, 2));
            $tahun = substr($tgl, 0, 4);
            return $nameofDay . $tanggal . ' ' . $bulan . ' ' . $tahun . ' ' . $pecah[1] . ' WIB';
        } else {
            $tgl = $pecah[0];
            $tanggal = substr($tgl, 8, 2);
            $bulan = Lib::getBulan(substr($tgl, 5, 2));
            $tahun = substr($tgl, 0, 4);
            return $nameofDay . $tanggal . ' ' . $bulan . ' ' . $tahun;
        }
    }
	
	 public static function createTree(&$list, $parent){
		$tree = array();
		foreach ($parent as $k=>$l){
			if(isset($list[$l['id']])){
				$l['children'] = Lib::createTree($list, $list[$l['id']]);
			}
			$tree[] = $l;
		} 
		return $tree;
	}	
	
	public static function buildTree(array $elements, $parentId = 0) {
		$branch = array();

		foreach ($elements as $element) {
			if ($element['parent_id'] == $parentId) {
				unset($element['parent_id']);
				
				$children = Lib::buildTree($elements, $element['id']);
				unset($element['id']);
				if ($children) {
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}

		return $branch;
	}
	
	public static function strukturView($struktur) {
		
		foreach($struktur as $key => $data) {
			$countchild = 2;
			if(!empty($data['children'])) {
				$countawal 	= count($data['children']);
				$countchild = count($data['children'])*2;
				$linechild 	= ((count($data['children'])*2)/2)-1;
			}
			
			echo '
				<td colspan="'.$countchild.'"><div class="node" node-id="1"><h2>'.$data['title'].'</h2><div class="org-add-button">Add Child</div><div class="org-del-button"></div></div></td>
			';
			
			
			if(!empty($data['children'])) {
				echo '
				<tr class="lines">
					<td colspan="'.$countchild.'">
						<table cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr class="lines x">
								<td class="line left half"></td>
								<td class="line right half"></td>
							</tr>
						</tbody>
						</table>
					</td>
				</tr>
				<tr class="lines v">
					<td class="line left"></td>';
					for ($x = 1; $x <= $linechild; $x++) {
					echo '
					<td class="line right top"></td>
					<td class="line left top"></td>
					';
					}
					echo'
					<td class="line right"></td>
				</tr>';
				
				
				echo' 
				
				<tr>
					<td colspan="'.$countchild.'">
						<table cellpadding="0" cellspacing="0" border="0">
							<tbody>
								<tr>';
								foreach($data['children'] as $anak) {
									echo '
										<td colspan="'.$countchild.'"><div class="node" node-id="1"><h2>'.$data['title'].'</h2><div class="org-add-button">Add Child</div><div class="org-del-button"></div></div></td>
									';
								}
								echo'
								</tr>
								';
								Lib::strukturView();
								echo'
							</tbody>
						</table>
					</td>
				</tr>
				';
			}
		}
    }
    public static function getPenjumlahanTime($time){

        $init = $time;
        $hours  = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;
        $isi = "$hours:$minutes:$seconds";
        return $isi;
    }

    function convertTimetoHours($str_time)
    {
        $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
        sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
        $hours = $time_seconds/(60*60);
        $hours = round($hours);
        return $hours;
    }

    function pembulatanJamLembur($str_time)
    {
        $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
        sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
        if($minutes >= 30) {
            $hours =  $hours+1;
        } else {
            $hours =  $hours;
        }
        return date('H:i:s',strtotime($hours.':00:00'));
    }

    function convertHourstoSecond($str_time)
    {
        $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
        sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
        return Lib::$time_seconds;
    }

    function convertSecondtoHours($second)
    {
        $hours = floor($second/(60*60));
        $mins = floor(($second-($hours*60*60))/(60));
        $secs = floor(($second-(($hours*60*60)+($mins*60))));
        if(strlen($hours)<2){$hours="0".$hours;}
        if(strlen($mins)<2){$mins="0".$mins;}
        if(strlen($secs)<2){$secs="0".$secs;}
        return $hours.':'.$mins.':'.$secs;
    }

    function getTimeDiff($dtime,$atime)
    {
        $nextDay = $dtime>$atime?1:0;
        $dep = explode(':',$dtime);
        $arr = explode(':',$atime);
        $diff = abs(mktime($dep[0],$dep[1],0,date('n'),date('j'),date('y'))-mktime($arr[0],$arr[1],0,date('n'),date('j')+$nextDay,date('y')));
        return $diff;
    }


    function getTimeDiffTo($diff)
    {
        $hours = floor($diff/(60*60));
        $mins = floor(($diff-($hours*60*60))/(60));
        $secs = floor(($diff-(($hours*60*60)+($mins*60))));
        if(strlen($hours)<2){$hours="0".$hours;}
        if(strlen($mins)<2){$mins="0".$mins;}
        if(strlen($secs)<2){$secs="0".$secs;}
        return $hours.':'.$mins.':'.$secs;
        return $diff;
    }

    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
    
        $string = array(
            'y' => 'tahun',
            'm' => 'bulan',
            'w' => 'minggu',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }
    
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' lalu' : 'barusan';
    }


	public function timeDiff($time1,$time2){
		if($time2<=$time1){
			$w1=explode(":",$time1);
			$w2=explode(":",$time2);
			
			$s1=($w1[0]*3600)+($w1[1]*60)+$w1[2];
			$s2=($w2[0]*3600)+($w2[1]*60)+$w2[2];
			
			$dif=$s1-$s2;
			
			$jam=floor($dif/3600);
			$menit=floor(($dif-($jam*3600))/60);
			$detik=$dif-($jam*3600)-($menit*60);
			$jam=str_pad((int) $jam,2,"0",STR_PAD_LEFT);
			$menit=str_pad((int) $menit,2,"0",STR_PAD_LEFT);
			$detik=str_pad((int) $detik,2,"0",STR_PAD_LEFT);
			
			return $jam.':'.$menit.':'.$detik;
		}else{
			$w1=explode(":",$time1);
			$w2=explode(":",$time2);
			
			$s1=($w1[0]*3600)+($w1[1]*60)+$w1[2];
			$s2=($w2[0]*3600)+($w2[1]*60)+$w2[2];
			
			$dif=$s2-$s1;
			
			$jam=floor($dif/3600);
			$menit=floor(($dif-($jam*3600))/60);
			$detik=$dif-($jam*3600)-($menit*60);
			$jam=str_pad((int) $jam,2,"0",STR_PAD_LEFT);
			$menit=str_pad((int) $menit,2,"0",STR_PAD_LEFT);
			$detik=str_pad((int) $detik,2,"0",STR_PAD_LEFT);
			
			return $jam.':'.$menit.':'.$detik;
		}
	}
	
	//Time1 masuk real & time2 masuk finger
	public function Late($time1,$time2){
		if($time2!=""){
			if($time2<=$time1){
				return "00:00:00";
			}else{
				$w1=explode(":",$time1);
				$w1[1]+=SysConfig::getValue('default_toleransi_telat_absen');//Toleransi 5 menit
				$w2=explode(":",$time2);
				
				$s1=($w1[0]*3600)+($w1[1]*60)+$w1[2];
				$s2=($w2[0]*3600)+($w2[1]*60)+$w2[2];
				
				if($s2<=$s1){
					return "00:00:00";
				}else{
					$dif=$s2-$s1;
					$jam=floor($dif/3600);
					$menit=floor(($dif-($jam*3600))/60);
					$detik=$dif-($jam*3600)-($menit*60);
					$jam=str_pad((int) $jam,2,"0",STR_PAD_LEFT);
					$menit=str_pad((int) $menit,2,"0",STR_PAD_LEFT);
					$detik=str_pad((int) $detik,2,"0",STR_PAD_LEFT);
					
					return $jam.':'.$menit.':'.$detik;
				}
			}
		}else{
			return'';
		}
	}
	//time1 waktu pulang real & time2 waktu pulang finger
	public function QuickHome($time1,$time2){
        if($time2<$time1){
            $w1=explode(":",$time1);
            $w2=explode(":",$time2);
            
            $s1=($w1[0]*3600)+($w1[1]*60)+$w1[2];
            $s2=($w2[0]*3600)+($w2[1]*60)+$w2[2];
            
            $dif=$s1-$s2;
            
            $jam=floor($dif/3600);
            $menit=floor(($dif-($jam*3600))/60);
            $detik=$dif-($jam*3600)-($menit*60);
            $jam=str_pad((int) $jam,2,"0",STR_PAD_LEFT);
            $menit=str_pad((int) $menit,2,"0",STR_PAD_LEFT);
            $detik=str_pad((int) $detik,2,"0",STR_PAD_LEFT);
            
            return $jam.':'.$menit.':'.$detik;
            
        }else{
            return "00:00:00";
        }
	}
	
	//time1 waktu pulang real & time2 waktu pulang finger
	public function OverTime($time1,$time2){
		if($time2>$time1){
			$w1=explode(":",$time1);
			$w2=explode(":",$time2);
			
			$s1=($w1[0]*3600)+($w1[1]*60)+$w1[2];
			$s2=($w2[0]*3600)+($w2[1]*60)+$w2[2];
			
			$dif=$s2-$s1;
			
			$jam=floor($dif/3600);
			$menit=floor(($dif-($jam*3600))/60);
			$detik=$dif-($jam*3600)-($menit*60);
			$jam=str_pad((int) $jam,2,"0",STR_PAD_LEFT);
			$menit=str_pad((int) $menit,2,"0",STR_PAD_LEFT);
			$detik=str_pad((int) $detik,2,"0",STR_PAD_LEFT);
			
			return $jam.':'.$menit.':'.$detik;
			
		}else{
			return "00:00:00";
		}
	}
	
	public static function TotalJam($time){
		$dif=0;
		foreach($time as $row){
			$w1=explode(":",$row);
			$dif+=($w1[0]*3600)+($w1[1]*60)+$w1[2];
		}	
		
		$jam=floor($dif/3600);
		$menit=floor(($dif-($jam*3600))/60);
		$detik=$dif-($jam*3600)-($menit*60);
		$jam=str_pad((int) $jam,2,"0",STR_PAD_LEFT);
		$menit=str_pad((int) $menit,2,"0",STR_PAD_LEFT);
		$detik=str_pad((int) $detik,2,"0",STR_PAD_LEFT);
		
		return $jam.':'.$menit.':'.$detik;
	}

	public function WO($attIn,$attOut,$shiftIn,$shiftOut){
		if(!empty($attIn) AND !empty($attOut)){
			if($shiftIn<$shiftOut){
				
				if($attIn<=$shiftIn){
					$start=$shiftIn;
				}else{
					$start=$attIn;
				}
				
				if($attOut>=$shiftOut){
					$end=$shiftOut;
				}else{
					if($attOut>'00:00:00'){
						$end=$shiftOut;
					}else{
						$end=$attOut;
					}
					
				}
				//tidak lintas hari
				$w1=explode(":",$start);
				$w2=explode(":",$end);
				
				$s1=($w1[0]*3600)+($w1[1]*60)+$w1[2];
				$s2=($w2[0]*3600)+($w2[1]*60)+$w2[2];
				
				$dif=$s2-$s1;
				
				$jam=floor($dif/3600);
				$menit=floor(($dif-($jam*3600))/60);
				$detik=$dif-($jam*3600)-($menit*60);
				$jam=str_pad((int) $jam,2,"0",STR_PAD_LEFT);
				$menit=str_pad((int) $menit,2,"0",STR_PAD_LEFT);
				$detik=str_pad((int) $detik,2,"0",STR_PAD_LEFT);
				
				return $jam.':'.$menit.':'.$detik;
				
			}else{
				
				if($attIn<=$shiftIn){
					$start=$shiftIn;
				}else{
					$start=$attIn;
				}
				
				if($attOut>=$shiftOut){
					$end=$shiftOut;
				}else{
					$end=$attOut;
				}
				//tidak lintas hari
				$w1=explode(":",$start);
				$w2=explode(":",$end);
				
				$change="24:00:00";
				$changeDay="00:00:00";
				$w3=explode(":",$change);
				$s2=($w2[0]*3600)+($w2[1]*60)+$w2[2];
				$s1=($w1[0]*3600)+($w1[1]*60)+$w1[2];
				$s3=($w3[0]*3600)+($w3[1]*60)+$w3[2];
				
				$dif=($s3-$s1)+$s2;
				
				$jam=floor($dif/3600);
				$menit=floor(($dif-($jam*3600))/60);
				$detik=$dif-($jam*3600)-($menit*60);
				$jam=str_pad((int) $jam,2,"0",STR_PAD_LEFT);
				$menit=str_pad((int) $menit,2,"0",STR_PAD_LEFT);
				$detik=str_pad((int) $detik,2,"0",STR_PAD_LEFT);
				
				return $jam.':'.$menit.':'.$detik;
				//lintas hari
			}
		}else{
			return "00:00:00";
		}
    }
    

    public function getCalenderDeretanTanggal($orgawal,$orgakhir){
        $tanggal1               = array();
        
        if(isset($orgawal) and isset($orgakhir)) {
            $orgawal                = date('Y-m-d', strtotime($orgawal));
            $orgakhir               = date('Y-m-d', strtotime($orgakhir));
            $awal                   = new DateTime($orgawal);
            $akhir                  = new DateTime($orgakhir);
            $jumlah_jadwal          = $awal->diff($akhir);
            for($x=0;$x<=$jumlah_jadwal->days;$x++){
                $tanggal1[] = date('Y-m-d', strtotime('+'.$x.' days', strtotime($orgawal)));
            }
        }
        return $tanggal1;

    }
}

