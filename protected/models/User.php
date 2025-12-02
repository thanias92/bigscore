<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property string $namaLengkap
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property SalesmanProfile $salesmanProfile
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    public $nama;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
        ];
    }



    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => 10]);
    }

    /**
     * @inheritdoc
     */
   public static function findIdentityByAccessToken($token, $type = null)
{
    try {
        $token = (new Parser())->parse((string) $token);
        $signer = new Sha256();
        $key = \Yii::$app->params['TokenEncryptionKey'];

        if (!$token->verify($signer, $key)) {
            \Yii::error("JWT token not verified", 'jwt');
            return null;
        }

        if ($token->isExpired()) {
            \Yii::error("JWT token expired", 'jwt');
            return null;
        }

        $uid = $token->getClaim('uid');
        \Yii::error("JWT token valid, UID: $uid", 'jwt');

        return static::findOne($uid);
    } catch (\Throwable $e) {
        \Yii::error("JWT parse error: " . $e->getMessage(), 'jwt');
        return null;
    }
}


    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    public static function isPasswordResetTokenValid($otp)
    {
        if (empty($otp)) {
            return false;
        }

        $user = self::findOne(['password_reset_token' => $otp]);

        if (!$user) {
            return false;
        }
        return true;
    }

    public function generatePasswordResetToken()
    {
        $otp = random_int(1000, 9999);  // Generate a random 4-digit number
        $this->password_reset_token = $otp;
    }



    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => 10]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => 10]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
        //     // return $password === '12345';
        //     return Yii::$app->security->validatePassword($password, $this->password_hash);

        // if (!$this->hasErrors()) {
        //     $user = $this->getUser();

        //     if (!$user) {
        //         $this->addError($attribute, 'Email not found.');
        //     } elseif (!$user->validatePassword($this->password)) {
        //         $this->addError($attribute, 'Password is incorrect.');
        //     }
        // }
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Gets query for [[SalesmanProfile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesmanProfile()
    {
        return $this->hasOne(SalesmanProfile::class, ['user_id' => 'id']);
    }
}
