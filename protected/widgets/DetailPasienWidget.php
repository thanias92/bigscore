<?php
// DetailPasienWidget.php

namespace app\widgets;

use yii\base\Widget;

class DetailPasienWidget extends Widget
{
    public $registrasi;

    public function run()
    {
        return $this->render('_detail_pasien', ['registrasi' => $this->registrasi]);
    }
}

?>