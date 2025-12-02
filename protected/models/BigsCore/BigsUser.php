<?php

namespace app\models\BigsCore;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class BigsUser extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return '{{%staff}}'; // Ganti dengan nama tabel user BIGS core kamu jika berbeda (misalnya 'staff')
    }

    public static function findIdentity($id)
    {
        return static::findOne($id); // Asumsi 'id' adalah primary key
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // Implementasikan jika menggunakan access token
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]); // Asumsi nama kolom username adalah 'username'
    }

    public function getId()
    {
        return $this->id_staff; // Ganti 'id_staff' dengan nama kolom primary key tabel staff kamu
    }

    public function getAuthKey()
    {
        return $this->auth_key; // Ganti 'auth_key' jika ada di tabel staff kamu
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return $password === $this->password; // Membandingkan password plain text
    }
}
