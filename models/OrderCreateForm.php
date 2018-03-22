<?php
/**
 * Created by PhpStorm.
 * User: lhh
 * Date: 17/3/24
 * Time: 上午9:56
 */

namespace app\models;


use yii\base\Model;

class OrderCreateForm extends Model
{

    public $lastid,$uid,$order_id,$guest_name,$title,$type,$total_len,$amount,$mobile,$qq,$note,$appointed_time;
    public function rules()
    {
        return [
            [['guest_name','title','total_len','type','amount','appointed_time'], 'required'],
            [['total_len'], 'integer'],
            [['amount'], 'number'],
            [['note'], 'string'],
            [['order_id'], 'string', 'max' => 16],
            [['title', 'mobile'], 'string', 'max' => 180],
            [['guest_name'], 'string', 'max' => 32],
            [['qq'], 'string', 'max' => 12],
        ];
    }
    public function save()
    {
        if($this->validate()){
            $order = new Orders();
            $order->uid = \Yii::$app->user->identity->id;
            $order->order_id = Orders::generateOrderId();
            $order->created_time = date('YmdHis');
            $order->update_time = $order->created_time;
            $order->title = $this->title;
            $order->type = $this->type;
            $order->guest_name = $this->guest_name;
            $order->amount = $this->amount;
            $order->appointed_time = $this->appointed_time;
            $order->mobile = $this->mobile;
            $order->qq = $this->qq;
            $order->total_len = $this->total_len;
            $order->note = $this->note;
            if($order->save()){
                $this->lastid = $order->id;
                Logs::createLog($order->order_id,'创建了订单');
                return true;
            }else{
                $this->addErrors($order->getErrors());
            }
        }


        return false;

    }

    public function getId()
    {

    }




    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => '论文标题',
            'guest_name' => '客户名称',
            'total_len' => '总字数',
            'cost' => '成本价',
            'amount' => '订单金额',
            'appointed_time' => '约定时间',
            'publish_time' => '交稿时间',
            'type' => '类型',
            'qq' => 'QQ',
            'mobile' => '联系方式',
            'note' => '备注',
        ];
    }
}