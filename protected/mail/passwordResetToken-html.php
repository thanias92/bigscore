<?php
/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'otp' => $user->password_reset_token]);
//$resetLink = $resetLink = "http://localhost:8080/v1/auth/reset-password?lp=$user->password_reset_token";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
        <h2 style="text-align: center; color: #333;">Reset Password Mobile Satu Kantor</h2>
        <p>Hello <?= $user->username ?>,</p>
        <p>Kami menerima permintaan untuk mereset password Mobile Satu Kantor Anda. Berikut adalah kode OTP untuk melakukan reset password Anda:</p>
        <h1 style="text-align: center; background-color: #f9f9f9; padding: 10px; border-radius: 5px; margin: 20px auto; width: fit-content;"><?= $user->password_reset_token ?></h1>
        <p>Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini. Kode OTP ini akan kedaluwarsa dalam beberapa waktu.</p>
        <p>Terima kasih</p>
    </div>
</body>
</html>
