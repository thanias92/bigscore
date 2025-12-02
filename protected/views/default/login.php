<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Login';
?>
<style>
  .footer {
    text-align: center;
    color: #777777;
    /* Warna abu-abu */
  }

  .logo {
    /* Atur lebar dan tinggi logo sesuai kebutuhan */
    width: 150px;
    height: 150px;
    /* Ganti URL dengan URL gambar logo */
    background-image: url('<?= Url::base(true); ?>/themes/images/greybigslogo.png');
    /* Atur posisi logo agar berada di tengah-tengah */
    background-repeat: no-repeat;
    background-size: contain;
    /* Agar logo tetap proporsional */
    margin: 0 auto;
    /* Membuat logo berada di tengah */
  }
  
  /* Tambahkan CSS untuk pesan error */
  .has-error .help-block {
    color: red; /* Warna merah untuk pesan error */
  }
</style>
<div class="wrapper">
  <div class="login-content" style="background: url(<?= Url::base(true); ?>/themes/images/dashboard/companybg.jpg); background-size: cover;background-repeat: no-repeat;">
    <div class="container">
      <div class="row d-flex align-items-center justify-content-center vh-100 w-100 m-0">
        <div class="col-lg-5 col-md-12 align-self-center">
          <div class="card p-0 mb-0">
            <div class="card-body auth-card">
              <div class="logo-img">
                <div class="logo-main col-sm-6 offset-3">
                  <img class="logo-normal img-fluid" src="<?= Url::base(true); ?>/themes/img/icon.png" alt="logo">
                </div>
              </div>
              <hr />
              <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

              <?= $form->field($model, 'email') ?>

              <?= $form->field($model, 'password')->passwordInput() ?>

              <div class="row">
                <div class="col-12">
                  <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div>
              </div>
              <div class="row">
                <div class="col-12 d-flex align-items-end flex-column">
                  <?= Html::submitButton('
                  <span class="btn-inner">
                    <span class="text d-inline-block align-middle">Log In</span>
                  </span>
                  ', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                  <br>
                </div>
              </div>
              <!-- <div class="footer">
                <h6>Powered by</h6>
                <div class="logo">
                </div>
                <h6>© 2024 PT Bigs Integrasi Teknologi</h6>
              </div> -->

              <?php ActiveForm::end(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>