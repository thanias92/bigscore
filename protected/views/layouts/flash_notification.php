<?php
if(Yii::$app->session->hasFlash('success')) {
  $msg = Yii::$app->session->getFlash('success');
  $this->registerJs(<<<JS
    iziToast.show({
        theme: 'light',
        icon: 'fas fa-check',
        iconColor: 'green',
        title: 'Notifikasi !',
        displayMode: 2,
        timeout: 3000,
        message: '$msg',
        position: 'topRight',
        transitionIn: 'flipInX',
        transitionOut: 'flipOutX',
        progressBarColor: 'rgb(0, 255, 184)',
        layout: 2,
    });
  JS
  );
}elseif(Yii::$app->session->hasFlash('info')) {
  $msg = Yii::$app->session->getFlash('info');
  $this->registerJs(<<<JS
    iziToast.show({
        theme: 'light',
        icon: 'fas fa-info-circle',
        iconColor: 'blue',
        title: 'Notifikasi !',
        displayMode: 2,
        timeout: 3000,
        message: '$msg',
        position: 'topRight',
        transitionIn: 'flipInX',
        transitionOut: 'flipOutX',
        progressBarColor: 'rgb(0, 255, 184)',
        layout: 2,
    });
  JS
  );
}elseif(Yii::$app->session->hasFlash('warning')) {
  $msg = Yii::$app->session->getFlash('warning');
  $this->registerJs(<<<JS
    iziToast.show({
        theme: 'light',
        icon: 'fas fa-exclamation-triangle',
        iconColor: 'orange',
        title: 'Notifikasi !',
        displayMode: 2,
        timeout: 3000,
        message: '$msg',
        position: 'topRight',
        transitionIn: 'flipInX',
        transitionOut: 'flipOutX',
        progressBarColor: 'rgb(0, 255, 184)',
        layout: 2,
    });
  JS
  );
}elseif(Yii::$app->session->hasFlash('error')) {
  $msg = Yii::$app->session->getFlash('error');
  $this->registerJs(<<<JS
    iziToast.show({
        theme: 'light',
        icon: 'fas fa-times-circle',
        iconColor: 'maroon',
        title: 'Notifikasi !',
        displayMode: 2,
        timeout: 3000,
        message: '$msg',
        position: 'topRight',
        transitionIn: 'flipInX',
        transitionOut: 'flipOutX',
        progressBarColor: 'rgb(0, 255, 184)',
        layout: 2,
    });
  JS
  );
}
?>
