<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $username
 * @property string $content
 * @property string $create_time
 * @property integer $order_id
 */
class Logs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_id'], 'integer'],
            [['content'], 'string'],
            [['create_time'], 'safe'],
            [['order_id'], 'required'],
            [['username'], 'string', 'max' => 32],
        ];
    }

    /**
     * @param $order_id
     * @param $content
     * @return bool
     */
    public static function createLog($order_id,$content,$name=null)
    {
        $log = new Logs();
        $log->uid = \Yii::$app->user->identity->id;
        $log->username = \Yii::$app->user->identity->username.'['.\Yii::$app->user->identity->real_name.']';
        $log->content = $content;
        $log->order_id = $order_id;
        $log->name = $name;
        $log->create_time = date('ymdHis');
        return $log->save();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'username' => 'Username',
            'content' => 'Content',
            'create_time' => 'Create Time',
            'order_id' => 'Order ID',
            'name' => '存入的名称',
        ];
    }
}
