<?php

namespace app\models;

use yii\base\Model;

class ReportFilterForm extends Model
{
    public $startDate;
    public $endDate;

    public function rules()
    {
        return [
            // Atur tanggal default ke 1 bulan terakhir jika kosong
            ['startDate', 'default', 'value' => date('Y-m-d', strtotime('-1 month'))],
            ['endDate', 'default', 'value' => date('Y-m-d')],
            [['startDate', 'endDate'], 'required'],
            [['startDate', 'endDate'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => 'Dari Tanggal',
            'endDate' => 'Sampai Tanggal',
        ];
    }
}
