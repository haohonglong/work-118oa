<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $uid
 * @property string $content
 * @property string $create_time
 * @property string $update_time
 * @property integer $status
 * @property integer $type
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'status','type'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['order_id'], 'string', 'max' => 16],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * 创建一条站内信息
     * @param $type
     * @param $order_id
     * @param string $content
     * @return bool
     */
    public static function create($type,$order_id,$content=""){
        $message = new Message();
        $message->type = $type;
        $message->order_id = $order_id;
        $message->uid = \Yii::$app->user->identity->id;
        $message->content = $content;
        $message->create_time = date('ymdHis');
        $message->update_time = $message->create_time;
        return $message->save();
    }

    /**
     * 修改信息为已读状态
     * @param $id
     * @return bool
     */
    public static function read($id){
        $message = static::find()->where(['id'=>$id])->limit(1)->one();
        $message->status = 1;
        $message->update_time = date('ymdHis');
        return $message->save();
    }

    /**
     * 状态为:完成;黄稿;取消 时根据订单号 读取所有对应订单号没读的信息为已读
     * @param $order_id
     */
    public static function readByOrder_id($order_id){
        $messages = static::find()->where(['order_id'=>$order_id])->all();
        $date = date('ymdHis');
        foreach ($messages as $item){
            $item->status = 1;
            $item->update_time = $date;
            $item->save();
        }
    }

    /**
     * 显示提示信息
     * @param $type
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function show($type=null){
        $query = static::find();
        if(isset($type)){
            $query->where(['type'=>$type,'status'=>0]);
        }else{
            $query->where(['status'=>0]);
        }
        return $query->indexBy('id')->orderBy(['id'=>SORT_DESC])->asArray()->all();
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单号',
            'uid' => '发送者',
            'content' => '信息内容',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'status' => '读取状态',
            'type' => '信息类型',
        ];
    }
}
