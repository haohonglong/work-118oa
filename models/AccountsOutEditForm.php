<?php

namespace app\models;

use Yii;
use yii\base\Model;


class AccountsOutEditForm extends Model
{
    public $amount,$pay_type,$pay_time,$note,$serial_number,$check_status,$in_address,$in_account_number,$in_openaccount,$in_zipcode,$in_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_time','amount','pay_type','in_account_number','in_name'], 'required'],
            [['in_openaccount','in_zipcode','in_address'],'filter', 'filter' => 'trim'],
            [['amount'], 'number'],
            [['note'], 'string'],
            [['serial_number'], 'string', 'max' => 64],
            [['pay_type'], 'string', 'max' => 32],
            [['in_name'], 'string', 'max' => 20],
        ];
    }
    public function save($id)
    {
        $account = Accounts::findById($id);

        if($this->validate()){
            $account->pay_type = $this->pay_type;
            $account->pay_time = $this->pay_time;
            $account->serial_number = $this->serial_number;
            $account->amount = $this->amount;
            $account->in_name = $this->in_name;
            $account->in_zipcode = $this->in_zipcode;
            $account->in_openaccount = $this->in_openaccount;
            $account->in_account_number = $this->in_account_number;
            $account->in_address = $this->in_address;
            $account->note = $this->note;
            $account->update_time = date('YmdHis');
            if($account->save()){
                $att=$this->attributeLabels();
                $msg='修改出账纪录';
                $msg.=' '.$att['pay_type'].'->'.$account->pay_type.';';
                $msg.=$att['serial_number'].'->'.$account->serial_number.';';
                $msg.=$att['in_name'].'->'.$account->in_name.';';
                $msg.=$att['in_openaccount'].'->'.$account->in_openaccount.';';
                $msg.=$att['in_account_number'].'->'.$account->in_account_number.';';
                $msg.=$att['in_address'].'->'.$account->in_address.';';
                $msg.=$att['in_zipcode'].'->'.$account->in_zipcode.';';
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
            'in_name' => '收款人名字',
            'in_address' => '收款人地址',
            'in_zipcode' => '收款人邮编',
            'in_account_number' => '收款人账号',
            'in_openaccount' => '收款人开户行',
            'note' => '备注',
        ];
    }
}
