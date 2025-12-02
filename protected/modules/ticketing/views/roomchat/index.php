<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use app\models\Roomchat;
use yii\web\View;

/** @var View \$this */
/** @var Roomchat[] \$roomList */
?>

<style>
  .chat-bubble {
    max-width: 60%;
    display: inline-block;
    padding: 8px 12px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.4;
  }
</style>

<div class="container mt-4">
  <div class="row">
    <!-- Roomlist Customer -->
    <div class="col-md-4 border-end">
      <h5 class="mb-3">Roomlist</h5>
      <ul class="list-group">
        <?php
        $uniqueCustomers = [];
        foreach ($roomList as $room):
          $customerId = $room->id_customer;
          if (in_array($customerId, $uniqueCustomers)) {
            continue;
          }
          $uniqueCustomers[] = $customerId;

          $customerName = isset($room->customer) ? $room->customer->customer_name : 'Unknown';
          $sendTime = $room->send_at ? Yii::$app->formatter->asDatetime($room->send_at) : '-';
        ?>
          <li class="list-group-item">
            <a href="#" class="text-decoration-none text-dark openChatRoom" data-id="<?= $customerId ?>">
              <div class="fw-bold"><?= Html::encode($customerName) ?></div>
              <small><?= Html::encode($sendTime) ?></small>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Chat Area -->
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <strong id="chatTitle">Chat</strong>
        </div>
        <div class="card-body" style="height: 400px; overflow-y: auto;">
          <div id="chatContent">
            <p class="text-muted text-center">Pilih customer di sebelah kiri untuk melihat isi percakapan.</p>
          </div>
        </div>

        <!-- Form Chat -->
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
            <button type="button" onclick="send_chat()" class="btn btn-primary">Kirim</button>
          </div>

          <?php ActiveForm::end(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<span id="url_chat" data-url="<?= Url::to(['roomchat/load-chat-json'])?>"></span>
<span id="url_send_chat" data-url="<?= Url::to(['roomchat/send-chat'])?>"></span>

<?php
$sendChatUrl = Url::to(['roomchat/send-chat']);
$loadChatUrl = Url::to(['roomchat/load-chat-json']);?>
<script>function send_chat() {
  const formData = $('#chatForm').serialize();

  $.post($("#url_send_chat").data("url"), formData, function(response) {
    if (response.success) {
      $('.openChatRoom[data-id=' + response.id_customer + ']').click();
      $('#chatMessage').val('');
    } else {
      alert('Gagal mengirim pesan');
    }
  });
}</script>
<?php 

$script = <<<'JS'
$(document).on('click', '.openChatRoom', function(e) {
  e.preventDefault();
  const customerId = $(this).data('id');
  $('#chatCustomerId').val(customerId);

  const token = localStorage.getItem('jwt_token');

  $.ajax({
    url: $("#url_chat").data("url"),
    type: 'GET',
    data: { id_customer: customerId },
    headers: {
      'Authorization': 'Bearer ' + token
    },
    success: function(response) {
      let html = '';
      response.messages.forEach(msg => {
        const align = msg.sender_type === 'staff' ? 'text-end' : 'text-start';
        const bubbleClass = msg.sender_type === 'staff' ? 'bg-primary text-white' : 'bg-light';
        html += `
          <div class="${align}">
            <div class="chat-bubble ${bubbleClass} mb-2">
              <strong>${msg.sender_name}</strong><br>
              ${msg.message}<br>
              <small>${msg.send_at}</small>
            </div>
          </div>
        `;
      });

      $('#chatContent').html(html);
      $('#chatTitle').text(response.customer_name);
      $('#chatContent').scrollTop($('#chatContent')[0].scrollHeight);
    },
    error: function(xhr) {
      alert('Gagal load chat: ' + xhr.responseText);
    }
  });
});



JS;

$this->registerJs($script);
?>