<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Menu;

$this->title = $name;
$this->context->layout = "main_error";
?>

<div class="row">
	<div class="col-sm-6 special">
		<img src="<?=Url::base()?>/themes/404-page-1.gif" class="img-fluid"/>
	</div>
	<div class="col-sm-6 pt-5">
		<h1 style="font-weight: bold" class="text-warning"><?=$this->title?></h1>

		<i class="lead">
			<?= nl2br(Html::encode($message)) ?>
			<br />
			<?php
				if (Yii::$app->user->identity && Yii::$app->user->identity->username) {
					echo str_replace(["{user}", "{modul}"],['<strong class="font-weight-bold">'.Yii::$app->user->identity->username.'</strong class="font-weight-bold">', '<strong class="font-weight-bold">'.$menu->name.'</strong class="font-weight-bold">'],'Hallo {user}<br/> Sepertinya anda mengalamai masalah saat menggunakan aplikasi, silahkan hubungi Administrator.');
				} else {
					echo '<br><br><h2>Silahkan Hubungi Administrator</h2>';
				}
			?>
		</i>
	</div>
</div>
