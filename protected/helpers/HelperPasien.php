<?php

namespace app\helpers;

use app\models\Pasien;

class HelperPasien
{
    public static function panggilan(string $jenis_kelamin, int $usia, string $status_pernikahan)
    {
        if ($jenis_kelamin == 'Laki-laki') {
            if ($usia < 18) {
                if ($status_pernikahan == 'Belum menikah') {
                    return 'Anak';
                } else {
                    return 'Tn';
                }
            } else {
                if ($status_pernikahan == 'Belum menikah') {
                    return 'Nn';
                } else {
                    return 'Tn';
                }
            }
        } elseif ($jenis_kelamin == 'Perempuan') {
            if ($usia < 18) {
                if ($status_pernikahan == 'Belum menikah') {
                    return 'Anak';
                } else {
                    return 'Ny';
                }
            } else {
                if ($status_pernikahan == 'Belum menikah') {
                    return 'Nn';
                } else {
                    // tambahkan pengecekan untuk status janda
                    if ($status_pernikahan == 'Janda') {
                        return 'Ny';
                    } else {
                        return 'Ny';
                    }
                }
            }
        }
    }

    /**
     * generate no rekam medis
     *
     * @return string
     */
    public static function noRekamMedis()
    {
        $latest_pasien = Pasien::find()->orderBy(['id' => SORT_DESC])->one();
        return sprintf("%07d", $latest_pasien->id + 1 ?? 1);
    }
}
