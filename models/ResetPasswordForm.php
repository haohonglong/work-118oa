<?php
/**
 * Created by PhpStorm.
 * User: haohonglong
 * Date: 17/4/11
 * Time: 上午11:33
 */

namespace app\models;

use yii;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $old_password,$password,$password_repeat,$username;

    /**
     * @var \app\models\User
     */
    private $_user;

    public function __construct(){
        $this->_user = Yii::$app->user->identity;
    }
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password','old_password','password_repeat'], 'required'],
            [['username'], 'filter', 'filter' => 'trim'],
            ['password', 'string', 'min' => 5],
            ['username', 'string', 'max' => 5],
            ['old_password', 'validateOldPassword'],
            ['password_repeat', 'compare','compareAttribute'=>'password'],

        ];
    }
    public function validateOldPassword($attribute){
        if (!$this->_user || !$this->_user->validatePassword($this->old_password)){
            $this->addError($attribute, '原始密码错误');
        }
    }
    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function changePassword()
    {
        if ($this->validate()) {
            $user = $this->_user;
//            $user->username = $this->username;
            $user->setPassword($this->password);
            return $user->save();
        } else {
            return false;
        }
    }


    public function attributeLabels(){
        return [
            'old_password' => '原始密码',
            'password' => '新密码',
            'password_repeat' => '确认密码',
            'username' => '用户名',
        ];
    }
}