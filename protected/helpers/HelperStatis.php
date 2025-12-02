<?php

namespace app\helpers;

use app\models\DataStatis;
use yii\helpers\ArrayHelper;

class HelperStatis
{
    /**
     * list data statis by jenis referensi
     *
     * @param string $jenis_referensi
     * @return DataStatis
     */
    public static function getRef(string $jenis_referensi)
    {
        return DataStatis::find()
            ->where(['jenis_referensi' => $jenis_referensi])
            ->orderBy(['nama' => SORT_ASC])
            ->all();
    }

    /**
     * list data statis by jenis referensi order data secara random
     *
     * @param string $jenis_referensi
     * @return DataStatis
     */
    public static function getRefRandom(string $jenis_referensi)
    {
        return DataStatis::find()
            ->where(['jenis_referensi' => $jenis_referensi])
            ->orderBy('RANDOM()')
            ->one();
    }

    /**
     * list data statis by jenis referensi untuk dropdown, dll
     *
     * @param string $jenis_referensi
     * @return array
     */
    public static function getRefOptions(string $jenis_referensi)
    {
        return ArrayHelper::map(self::getRef($jenis_referensi), 'id', 'nama');
    }
}
