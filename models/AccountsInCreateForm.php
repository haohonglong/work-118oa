<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "accounts".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $uid
 * @property double $amount
 * @property string $pay_type
 * @property string $pay_time
 * @property string $create_time
 * @property integer $account_type
 * @property integer $check_status
 * @property string $note
 */
class AccountsInCreateForm extends Model
{
    public $order_id,$amount,$pay_type,$pay_time,$note,$serial_number,$check_status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id','pay_time','amount','pay_type'], 'required'],
            [['amount'], 'number'],
            [['note'], 'string'],
            [['serial_number'], 'string', 'max' => 64],
            [['pay_type'], 'string', 'max' => 32],
        ];
    }
    public function save()
    {
        $account = new Accounts();

        if($this->validate()){
            $account->uid = Yii::$app->user->identity->id;
            $account->pay_type = $this->pay_type;
            $account->order_id = $this->order_id;
            $account->pay_time = $this->pay_time;
            $account->serial_number = str_replace(' ', '',trim($this->serial_number));
            $account->account_type = 1;
            $account->amount = $this->amount;
            $account->note = $this->note;
            $account->create_time = date('YmdHis');
            $account->update_time = $account->create_time;
            if($account->save()){
                $att=$this->attributeLabels();
                $msg='添加入账纪录';
                $msg.=' '.$att['pay_type'].'->'.$account->pay_type.';';
                $msg.=$att['serial_number'].'->'.$account->serial_number.';';
                $msg.=$att['amount'].'->'.$account->amount.';';
                $msg.=$att['pay_time'].'->'.$account->pay_time.';';
                Logs::createLog($account->order_id,$msg);
                return true;
            }else{
                $this->addErrors($account->getErrors());
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单编号id',
            'serial_number' => '流水号',
            'amount' => '金额',
            'pay_type' => '转账方式',
            'pay_time' => '转账时间',
            'account_type' => '账务类型',
            'note' => '备注(请填写定金详情)',
        ];
    }
}
