<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "auth_assignment".
 *
 * @property string $item_name
 * @property string $user_id
 * @property integer $created_at
 *
 * @property AuthItem $itemName
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
//        return [
//            [['item_name', 'user_id'], 'required'],
//            [['created_at'], 'integer'],
//            [['item_name', 'user_id'], 'string', 'max' => 64],
//            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['item_name' => 'name']],
//        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => 'Item Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
//        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }

    /**
     * 根据角色名获取用户名及用户id
     * @param $roleName
     * @return array
     */
    public static function getUsersByRole($roleName)
    {
        if (empty($roleName)) {
            return [];
        }

        return (new Query())->select('u.id,u.real_name,u.username')
            ->from('auth_assignment as auth')
            ->innerJoin('user as u','auth.user_id = u.id')
            ->where(['auth.item_name' => $roleName])->all();
    }
}
