<?php
namespace app\components;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Yii;

class JwtHelper
{
    public static function generateJwt($user)
    {
        $signer = new Sha256();
        $key = new Key(Yii::$app->params['TokenEncryptionKey']); // Gunakan secret key dari params.php
        $time = time();

        $token = (new Builder())
            ->setIssuer(Yii::$app->params['JwtIssuer'])          // set issuer (iss)
            ->setAudience(Yii::$app->params['JwtAudience'])        // set audience (aud)
            ->setId('bigs-app', true)                // set token id (jti)
            ->setIssuedAt($time)                     // iat
            ->setExpiration($time + 7200)            // exp
            ->set('uid', $user->id)                  // custom claim
            ->set('username', $user->username)       // custom claim
            ->sign($signer, $key)                    // sign token
            ->getToken();                            // get final token

        return (string)$token;
    }
}
