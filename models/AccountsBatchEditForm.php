<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

/**
 * This is the model class for table "accounts".
 *
 * @property integer $id
 * @property string $pay_time
 * @property integer $check_status
 */
class AccountsBatchEditForm extends Model
{
    public $serial_number,$pay_time;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_time','serial_number'], 'filter', 'filter' => 'trim'],
            [['serial_number'], 'string', 'max' => 64],
        ];
    }
    public function save($ids,$account_type)
    {
        $db=Yii::$app->db;
        $att = $this->attributeLabels();
        $transaction = $db->beginTransaction();
        $id_arr = explode(',',$ids);
        $id_len = count($id_arr);
        $data=['check_status'=>3,'pay_time'=>$this->pay_time,'serial_number'=>$this->serial_number];
        $rows = Accounts::updateAll($data,['id'=>$id_arr,'check_status'=>1]);
        if($rows === $id_len){
            $transaction->commit();
            if($rows){
                $queries = Accounts::find()->select('id,check_status,pay_time,serial_number,order_id,amount')->where(['id'=>explode(',',$ids),'check_status'=>3])->all();
                foreach ($queries as $item){
                    Orders::updateCost($item->order_id,$account_type,$item->amount);
                    $msg='';
                    $msg.='转账成功';
                    $msg.=$att['pay_time'].'->';
                    $msg.=$item->pay_time;
                    $msg.=$att['serial_number'].'->';
                    $msg.=$item->serial_number;
                    $msg.='订单号->';
                    $msg.=$item->order_id;
                    Logs::createLog($item->order_id,$msg);
                }
                return true;
            }
        }else{
            $transaction->rollBack();
        }

        return false;
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'serial_number' => '流水号',
            'amount' => '金额',
            'pay_type' => '转账方式',
            'pay_time' => '转账时间',
            'in_name' => '收款人名字',
            'in_address' => '收款人地址',
            'in_zipcode' => '收款人邮编',
            'in_account_number' => '收款人账号',
            'in_openaccount' => '收款人开户行',
            'note' => '备注',
        ];
    }
}
