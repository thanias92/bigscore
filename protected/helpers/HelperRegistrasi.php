<?php

namespace app\helpers;

use app\models\Perjanjian;
use app\models\Registrasi;
use Carbon\Carbon;

class HelperRegistrasi
{
    public static function generateNoRegistrasi()
    {
        $latest_regis = Registrasi::find()->orderBy(['id' => SORT_DESC])->one();
        return Carbon::now()->format('Ymdhis') . '-' . sprintf("%05d", ($latest_regis ? $latest_regis->id : 0) + 1 ?? 1);
    }

    public static function generateNoRekamMedis()
    {
        $latest_regis = Registrasi::find()->orderBy(['id' => SORT_DESC])->one();
        return Carbon::now()->format('Ymd') . '-' . sprintf("%05d", $latest_regis->id + 1 ?? 1);
    }

    /**
     * Generate Kode Booking Online
     *
     * @return string
     */
    public static function generateKodeBooking()
    {
        $latest = Perjanjian::find()->orderBy(['id' => SORT_DESC])->one();
        return Carbon::now()->format('Ymdhis') . '-' . sprintf("%05d", ($latest ? $latest->id : 0) + 1 ?? 1);
    }
}
