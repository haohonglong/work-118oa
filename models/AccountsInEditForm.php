<?php

namespace app\models;

use Yii;
use yii\base\Model;


class AccountsInEditForm extends Model
{
    public $amount,$pay_type,$pay_time,$note,$serial_number;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_time','amount','pay_type'], 'required'],
            [['amount'], 'number'],
            [['note'], 'string'],
            [['serial_number'], 'string', 'max' => 64],
            [['pay_type'], 'string', 'max' => 32],
        ];
    }
    public function save($id)
    {
        $account = Accounts::findById($id);

        if(isset($account) && $this->validate()){
            $account->pay_type = $this->pay_type;
            $account->pay_time = $this->pay_time;
            $account->serial_number = str_replace(' ', '',trim($this->serial_number));
            $account->amount = $this->amount;
            $account->note = $this->note;
            $account->update_time = date('YmdHis');
            if($account->save()){
                $att=$this->attributeLabels();
                $msg='修改入账纪录';
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
            'serial_number' => '流水号',
            'amount' => '金额',
            'pay_type' => '转账方式',
            'pay_time' => '转账时间',
            'note' => '备注(请填写定金详情)',
        ];
    }
}
