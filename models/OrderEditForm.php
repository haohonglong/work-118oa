<?php
/**
 * Created by PhpStorm.
 * User: lhh
 * Date: 17/3/24
 * Time: 上午9:56
 */

namespace app\models;


use yii\base\Model;

class OrderEditForm extends Model
{

    public $id,$after_sale,$uid,$order_id,$guest_name,$title,$type,$total_len,$amount,$mobile,$qq,$note,$appointed_time,$update_time,$status,$workflow,$profit,$publish_time,$finished;
    public function rules()
    {

        return [
            [['guest_name','title','total_len','type','amount','appointed_time','workflow','status'], 'required'],
            [['total_len'], 'integer'],
            [['publish_time','after_sale'], 'filter', 'filter' => 'trim'],
            [['amount'], 'number'],
            [['note'], 'string'],
            [['order_id'], 'string', 'max' => 16],
            [['title', 'mobile'], 'string', 'max' => 180],
            [['guest_name'], 'string', 'max' => 32],
            [['qq'], 'string', 'max' => 12],
            [['status'], 'in', 'range' => array_keys(\Yii::$app->params['orders_status'])],
            [['workflow'], 'in', 'range' => array_keys(\Yii::$app->params['orders_workflow'])],
        ];
    }

    /**
     * @param $id   订单id
     * @return bool
     */
    public function save($id)
    {

        if($this->validate()){
            $order = Orders::find()->where(['id'=>$id])->limit(1)->one();
            if(2 == $this->status && !(8 == $order->workflow || 8 == $this->workflow)){
                $this->status = 1;
                return false;
            }
            $order_arr = $order->toArray();
            $order->update_time = date('YmdHis');
            //指定售后
            if(3 == $order->workflow && 4 == $this->workflow && \Yii::$app->user->can('p_after_sale') || \Yii::$app->user->can('p_leader')){
                if(isset($this->after_sale) && !empty($this->after_sale) || \Yii::$app->user->can('p_leader')){
                    $order->after_sale = $this->after_sale;
                }else{
                    $order->after_sale = \Yii::$app->user->identity->username;
                }
            }

            //工作流超过第4步,且没指定售后,就指定当前登入的售后
            if($this->workflow > 4 && \Yii::$app->user->can('p_after_sale') && !\Yii::$app->user->can('p_leader') && !\Yii::$app->user->can('p_admin')){
                if(empty($order->after_sale)){
                    $order->after_sale = \Yii::$app->user->identity->username;
                }
            }


            if(\Yii::$app->user->can('after_sale') || \Yii::$app->user->can('p_front_sale') && 1 == $order->workflow){
                $order->title = $this->title;
                $order->type = $this->type;
                $order->guest_name = $this->guest_name;
                $order->amount = $this->amount;
                $order->appointed_time = $this->appointed_time;
                $order->mobile = $this->mobile;
                $order->qq = $this->qq;
                $order->total_len = $this->total_len;
                $order->publish_time = $this->publish_time;
                $order->note = $this->note;
            }

            if(
                1 == ($this->workflow - $order->workflow) &&
                \Yii::$app->user->can('p_leader') && !(3 == $this->workflow || 7 == $this->workflow) ||
                \Yii::$app->user->can('p_after_sale') && !(2 == $this->workflow || 3 == $this->workflow || 7 == $this->workflow) ||
                \Yii::$app->user->can('p_accounter') && ((2 == $order->workflow && 3 == $this->workflow) || (6 == $order->workflow && 7 == $this->workflow)) ||
                \Yii::$app->user->can('p_front_sale') && (1 == $order->workflow && 2 == $this->workflow)//售前只能改工作流2
            ){
                $order->workflow = $this->workflow;
            }

            if(\Yii::$app->user->can('p_after_sale')){
                $order->status = $this->status;
            }

            if(2 == $order->status){
                $order->finished = $order->update_time;
            }
            if($order->save()){
                $msg = '修改订单状态成';
                switch ($this->status){
                    case 3:
                        $msg .='取消';
                        Logs::createLog($order->order_id,$msg);
                        break;
                    case 2:
                        $msg .='完成';
                        Logs::createLog($order->order_id,$msg);
                        break;
                    case 4:
                        $msg .='黄稿';
                        Logs::createLog($order->order_id,$msg);
                        break;
                    case 5:
                        $msg .='退稿';
                        Logs::createLog($order->order_id,$msg);
                        break;


                    default:

                }

                switch ($this->status){
                    case 3:
                    case 2:
                    case 4:
                    case 5:
                        Message::readByOrder_id($order->order_id);
                        break;
                    default:

                }

                switch ($order->workflow){
                    case 2:
                    case 6:
                        Message::create(2,$order->order_id,'财务');
                        break;
                    case 3:
                    case 7:
                        if(!empty($order->after_sale)){
                            Message::create(1,$order->order_id,'售后'.User::getRealName($order->after_sale));
                        }else{
                            Message::create(1,$order->order_id,'客服');
                        }

                        break;

                    default:

                }
                $this->compare($order_arr,$order->toArray(),$order->order_id);
                return true;
            }else{
                $this->addErrors($order->getErrors());
            }
        }


        return false;

    }

    /**
     * @param $old_order    没被修改的数据
     * @param $order        修改后的数据
     * @param $order_id     订单编号
     */
    private function compare($old_order,$order,$order_id)
    {
        $att = $this->attributeLabels();
        $ignore=['update_time'];
        $workflow =\Yii::$app->params['orders_workflow'];
        $orders_status =\Yii::$app->params['orders_status'];
        foreach ($old_order as $k =>$v){
            if($old_order[$k] != $order[$k] && !in_array($k,$ignore)){
                if('workflow' === $k){
                    $content=$workflow[$old_order[$k]] .'被修改为'. $workflow[$order[$k]];
                }elseif('status' === $k){
                    $content=$orders_status[$old_order[$k]] .'被修改为'. $orders_status[$order[$k]];
                }else{
                    if('after_sale' === $k){//售后确认
                        $content=$old_order[$k] .'确认为'. $order[$k].'['.User::getRealName($order[$k]).']';
                    }else{
                        $content=$old_order[$k] .'被修改为'. $order[$k];

                    }

                }
                Logs::createLog($order_id,$content,$att[$k]);
            }
        }
    }




    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => '论文标题',
            'guest_name' => '客户名称',
            'after_sale' => '售后客服',
            'total_len' => '总字数',
            'amount' => '订单金额',
            'workflow' => '工作流',
            'appointed_time' => '约定时间',
            'publish_time' => '写手交稿时间',
            'finished' => '订单完成时间',
            'type' => '类型',
            'qq' => 'QQ',
            'mobile' => '联系方式',
            'status' => '状态',
            'note' => '订单备注',
        ];
    }
}