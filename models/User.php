<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;


class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    public $authKey;

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
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username'=>$username])->limit(1)->one();

    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if(empty($this->password)){
            return false;
        }
        return \Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }


    /**
     * 获取所有售后
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAfterSaleAll(){

        return AuthAssignment::getUsersByRole('after_sale');
    }

    /**
     * 获取所有售前
     * @return array
     */
    public static function getFrontSaleAll(){

        return AuthAssignment::getUsersByRole('front_sale');
    }


    /**
     * username反向取出real_name
     * @param $username
     * @return bool|mixed
     */
    public static function getRealName($username)
    {
        $query = static::find()->select('real_name,username')->where(['username'=>$username])->limit(1)->one();
        if(isset($query->real_name)){
            return $query->real_name;
        }
        return false;
    }




}
