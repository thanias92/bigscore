<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */

$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
  $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\web\View;
use app\widgets\JSRegister;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

  <?= "<?php " ?>$form = ActiveForm::begin([
  'enableClientScript' => true,
  'type' => ActiveForm::TYPE_HORIZONTAL,
  'id' => 'form',
  'formConfig' => [
  'labelSpan' => 3,
  'deviceSize' => ActiveForm::SIZE_X_SMALL,
  'options' => ['class' => 'mb-1']
  ]
  ]); ?>
  <div class="row">
    <?php foreach ($generator->getColumnNames() as $attribute) {
      if (in_array($attribute, $safeAttributes)) {
        echo '<div class="col-4">';
        echo "\n";
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n";
        echo '</div>';
        echo "\n";
      }
    } ?>
  </div>
  <hr />
  <div class="form-group d-flex align-items-end flex-column">
    <?= "<?= " ?>Html::submitButton('<i class="fa fa-save"></i> '.<?= $generator->generateString('Simpan') ?>, ['class' => 'btn btn-primary btn-sm', "id"=>"btn-save"]) ?>
  </div>

  <?= "<?php " ?>ActiveForm::end(); ?>

</div>


<?= '<?php' ?>
<?= "\n" ?>
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  $('#form').submit(function(e) {
    e.preventDefault()
  });
  $(document).on("click", "#btn-save", function() {
    var link_simpan = $('#form').attr('action');
    var data_form = $('#form').serializeArray();

    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah Akan Menyimpan Data?',
      showCancelButton: true,
      confirmButtonText: 'Lanjutkan',
      cancelButtonText: 'Batal',
      icon: 'question',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: link_simpan,
          data: data_form,
          dataType: "html",
          success: function(response) {
            const res = JSON.parse(response);
            if (res.status == 'success') {
              $('#modal').modal('hide');
              Swal.fire('OK', res.message, 'success');
              $.pjax.reload({
                container: "#gridData<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>"
              });
            } else {
              Swal.fire('Terjadi Kesalahan.', res.message, 'warning');
            }
          }
        });
      }
    })
  });
</script>
<?= '<?php' ?> JSRegister::end(); ?>
