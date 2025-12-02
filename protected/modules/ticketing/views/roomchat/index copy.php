<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
?>

<div class="container mt-4">
  <div class="row">
    <!-- Bagian daftar chat -->
    <div class="col-md-4 border-end">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Chats</h5>
      </div>
      <ul class="list-group">
        <?php foreach ($roomList as $room): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <?= Html::a(Html::encode($room->customer->customer_name ?? 'Unknown'), '#', [
                'class' => 'text-decoration-none text-dark openChatRoom',
                'data-id' => $room->id_customer,
              ]) ?><br>
              <small><?= Html::encode(Yii::$app->formatter->asDatetime($room->send_at)) ?></small>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Bagian isi chat -->
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <strong>Chat</strong>
        </div>
        <div class="card-body" style="height: 400px; overflow-y: auto;">
          <div id="chatContent">
            <p class="text-muted text-center">Pilih roomchat di sebelah kiri untuk melihat isi chat.</p>
          </div>
        </div>

        <?php foreach ($roomList as $chat): ?>
          <div class="p-2 mb-2 rounded <?= $chat->id_staff == Yii::$app->user->id ? 'bg-primary text-white text-end' : 'bg-light text-start' ?>">
            <div>
              <strong><?= $chat->id_staff ? 'Staff' : 'Customer' ?>:</strong>
              <?= nl2br(Html::encode($chat->chat)) ?>
              <br>
              <small class="text-muted"><?= Yii::$app->formatter->asDatetime($chat->send_at) ?></small>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="card-footer">
          <?php $form = ActiveForm::begin([
            'id' => 'chatForm',
            'type' => ActiveForm::TYPE_INLINE,
            'action' => ['roomchat/send-chat'],
            'options' => ['class' => 'w-100'],
          ]); ?>

          <?= Html::hiddenInput('id_customer', '', ['id' => 'chatCustomerId', 'name' => 'id_customer']) ?>
          <div class="input-group w-100">
            <?= Html::textInput('chat', '', [
              'id' => 'chatMessage',
              'class' => 'form-control',
              'placeholder' => 'Tulis pesan...'
            ]) ?>
            <button type="submit" class="btn btn-primary">Kirim</button>
          </div>

          <?php ActiveForm::end(); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$sendChatUrl = Url::to(['roomchat/send-chat']);
$loadChatUrl = Url::to(['roomchat/load-chat']);
$script = <<<JS
$(document).on('click', '.openChatRoom', function(e) {
  e.preventDefault();
  const customerId = $(this).data('id');
  $('#chatCustomerId').val(customerId);

  $.ajax({
   url: '$loadChatUrl',
    type: 'GET',
    data: { id_customer: customerId },
    success: function(response) {
      $('#chatContent').html(response);
      $('#chatContent').scrollTop($('#chatContent')[0].scrollHeight); // 👈 tambahkan ini
    }
  });
});

$('#chatForm').on('submit', function(e) {
  e.preventDefault();
  const formData = $(this).serialize();
  console.log("Form Data: ", formData); // 👈 Tambahkan ini

  $.post('$sendChatUrl', formData, function(response) {
    console.log("Response: ", response); // 👈 Tambahkan ini
    if (response.success) {
      $('.openChatRoom[data-id=' + response.id_customer + ']').click();
      $('#chatMessage').val('');
    } else {
      alert('Gagal mengirim pesan');
    }
  });
});

JS;
$this->registerJs($script);
?>