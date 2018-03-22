<?php

namespace app\models;

use Yii;
use yii\db\Query;

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
 * @property string $update_time
 * @property integer $account_type
 * @property string $serial_number
 * @property integer $check_status
 * @property string $note
 */
class Accounts extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accounts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id','uid','pay_time', 'create_time','update_time', 'account_type','amount'], 'required'],
            [['uid', 'account_type','order_id'], 'integer'],
            [['amount'], 'number'],
            [['pay_time', 'create_time','update_time'], 'safe'],
            [['note'], 'string'],
            [['serial_number'], 'string','max' => 64],
            [['pay_type'], 'string', 'max' => 32],
            [['in_account_number'], 'string', 'max' => 55],
        ];
    }

    public static function findById($id)
    {
        return static::find()->where(['id'=>$id])->limit(1)->one();
    }

    /**
     * 获取账单列表
     * @param $account_type     账务类型：1：入；2:出
     * @return mixed
     */
    public static function getAccounts($account_type)
    {

        $model = (new Query())
            ->from(['a' => static::tableName()])
            ->select('a.id,a.amount,a.pay_type,a.pay_time,a.account_type,a.serial_number,a.check_status,a.in_account_number,a.in_name,a.order_id,a.note')
            ->addSelect('o.workflow,o.status,o.title,o.id as oid')
            ->innerJoin(['o'=>Orders::tableName()],'o.order_id = a.order_id')
            ->where(['and',['a.check_status'=>1,'o.status'=>1,'a.account_type'=>$account_type]])->all();
        $list =[];
        foreach ($model as $k=>$v){
            $sign = static::sign($v['in_name'],$v['pay_type'],$v['in_account_number']);
            $list[$sign][] = $v;
        }
        $var['model'] = $model;
        $var['list'] = $list;
        return $var;
    }



    /**
     * 签名算法
     * @param $name
     * @param $type
     * @param $number
     * @return string
     */
    public static function sign($name,$type,$number)
    {
        $arr=[
            'name'=>$name,
            'type'=>$type,
            'number'=>$number,
        ];
        ksort($arr);
        return md5(http_build_query($arr));

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单id',
            'serial_number' => '流水号',
            'amount' => '金额',
            'pay_type' => '转账方式',
            'pay_time' => '转账时间',
            'account_type' => '账务类型',
            'check_status' => '审核状态',
            'note' => '账单备注',
            'in_name' => '收款人名字',
            'in_address' => '收款人地址',
            'in_zipcode' => '收款人邮编',
            'in_account_number' => '收款人账号',
            'in_openaccount' => '收款人开户行',
        ];
    }
}
