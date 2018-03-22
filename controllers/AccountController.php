<?php
/**
 * Created by PhpStorm.
 * User: macmini
 * Date: 17/3/30
 * Time: 下午1:33
 */

namespace app\controllers;

use app\models\Accounts;
use app\models\AccountsBatchEditForm;
use app\models\AccountsInEditForm;
use app\models\AccountsOutEditForm;
use app\models\AccountsInCreateForm;
use app\models\AccountsOutCreateForm;
use app\models\Orders;
use app\models\Message;
use app\models\Logs;
use yii;
use yii\web\Controller;
use yii\filters\AccessControl;


class AccountController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true,'roles' => ['@']]
                ],
            ],
        ];
    }

    /**
     * @param $account_type 账务类型：1：入；2:出
     * @return mixed
     */
    private function getAccounts($account_type)
    {
        $request = Yii::$app->request;
        $accountsBatchEditForm = new AccountsBatchEditForm();
        if($request->isPost){
            $ids = $request->post('accountsBatchEditForm')['ids'];
            if($accountsBatchEditForm->load(Yii::$app->request->post()) && $accountsBatchEditForm->save($ids,$account_type)){
                $this->refresh();
            }
        }
        $var = Accounts::getAccounts($account_type);
        $var['accountsBatchEditForm'] = $accountsBatchEditForm;
        return $var;
    }

    /**
     * 添加入账
     * @return string
     */
    public function actionSaveInAccount()
    {

        $request = Yii::$app->request;
        if($request->isAjax){
            $model = new AccountsInCreateForm();
            if ($model->load($request->post()) && $model->save()){
                $var=[
                    'status'=>1,
                    'msg'=>'添加成功',
                ];
                $order = Orders::find()->where(['order_id'=>$model->order_id])->limit(1)->one();
                if($order->workflow > Orders::$last_work){//工作流到最后一步,根据客服添加账单,生成提示信息
                    Message::create(2,$model->order_id,'财务');
                }

            }else{
                $var=[
                    'status'=>0,
                    'msg'=>'添加失败',
                    'errors'=>$model->getErrors(),
                ];

            }
            echo json_encode($var);
        }

    }

    /**
     * 申请出账
     * @return string
     */

    public function actionSaveOutAccount()
    {
        $model = new AccountsOutCreateForm();

        $request = Yii::$app->request;
        if($request->isAjax){
            if ($model->load($request->post()) && $model->save()){
                $var=[
                    'status'=>1,
                    'msg'=>'添加成功',
                ];
                $order = Orders::find()->where(['order_id'=>$model->order_id])->limit(1)->one();
                if($order->workflow > Orders::$last_work){//工作流到最后一步,根据客服添加账单,生成提示信息
                    Message::create(2,$model->order_id,'财务');
                }
            }else{
                $var=[
                    'status'=>0,
                    'msg'=>'添加失败',
                    'errors'=>$model->getErrors(),
                ];

            }
            echo json_encode($var);
        }

    }


    public function actionOut()
    {
        $var = $this->getAccounts(2);
        return $this->render('out',$var);
    }
    public function actionIn()
    {
        $var = $this->getAccounts(1);
        return $this->render('in',$var);
    }
    /**
     * 根据id获取转账数据
     */
    public function actionGetAccountById()
    {
        $request=Yii::$app->request;
        if(Yii::$app->request->isAjax) {
            $id=$request->post('id');
            $query = Accounts::findOne($id);
            if($query){
                $var=[
                    'status'=>1,
                    'msg'=>'数据获取成功',
                    'data'=>$query->toArray(),
                ];
            }else{
                $var=[
                    'status'=>0,
                    'msg'=>'数据获取失败',
                    'errors'=>$query->getErrors(),
                ];
            }
            echo json_encode($var);
        }
    }

    /**
     *修改出账单
     */
    public function actionAccountOutEdit()
    {
        if(Yii::$app->request->isAjax) {
            $request=Yii::$app->request;
            $post=$request->post();
            $arr=$post;
            $id=$post['id'];
            unset($arr['id']);
            $model = new AccountsOutEditForm();
            if($model->load($arr) && $model->save($id)){
                $var=[
                    'status'=>1,
                    'msg'=>'修改成功',
                ];
            }else{
                $var=[
                    'status'=>0,
                    'msg'=>'修改失败',
                    'errors'=>$model->getErrors(),
                ];
            }
            echo json_encode($var);
        }
    }

    /**
     *修改入账单
     */
    public function actionAccountInEdit()
    {
        if(Yii::$app->request->isAjax) {
            $request=Yii::$app->request;
            $post=$request->post();
            $arr=$post;
            $id=$post['id'];
            unset($arr['id']);
            $model = new AccountsInEditForm();
            if($model->load($arr) && $model->save($id)){
                $var=[
                    'status'=>1,
                    'msg'=>'修改成功',
                ];
            }else{
                $var=[
                    'status'=>0,
                    'msg'=>'修改失败',
                    'errors'=>$model->getErrors(),
                ];
            }
            echo json_encode($var);
        }
    }

    /**
     * 会计确认收到款,修改审核状态
     */
    public function actionConfirmMony()
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            $id= $request->post('id');
            $order_id= $request->post('order_id');
            $query = Accounts::findOne($id);
            if(3 === $query->check_status){
                $var=[
                    'status'=>0,
                    'msg'=>'已经审核过了',
                ];
            }else{
                $query->check_status = 3;
                if ($query->save() && Orders::updateCost($order_id)){
                    $var=[
                        'status'=>1,
                        'msg'=>'成功',
                    ];
                    Logs::createLog($order_id,'财务审核成功','操作');
                    $order = Orders::find()->where(['order_id'=>$order_id])->limit(1)->one();
                    if($order->workflow > Orders::$last_work){//工作流到最后一步,才根据会计确认,提示信息
                        Message::create(1,$order_id,'客服');
                    }
                }else{
                    $var=[
                        'status'=>0,
                        'msg'=>'失败',
                        'errors'=>$query->getErrors(),
                    ];
                    Logs::createLog($order_id,'财务审核失败','操作');

                }
            }

            echo json_encode($var);
        }
    }

    /**
     * 会计拒绝这笔款
     */
    public function actionDenyMony()
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            $id= $request->post('id');
            $order_id= $request->post('order_id');
            $query = Accounts::findOne($id);
            $query->check_status=2;
            if ($query->save(false)){
                $var=[
                    'status'=>1,
                    'msg'=>'成功',
                ];
                Logs::createLog($order_id,'审核拒绝成功','操作');
            }else{
                $var=[
                    'status'=>0,
                    'msg'=>'失败',
                    'errors'=>$query->getErrors(),
                ];
                Logs::createLog($order_id,'审核拒绝失败','操作');

            }
            echo json_encode($var);
        }
    }

    /**
     * 显示所有转账纪录
     */
    public function actionShowAccountList()
    {
        $request = Yii::$app->request;
        if($request->isAjax) {
            $query = Accounts::find()->select('accounts.id,uid,user.real_name,pay_time,pay_type,note,account_type,check_status,order_id,amount,accounts.serial_number')
                ->orderBy('create_time DESC')
                ->innerJoin('user','user.id=accounts.uid')
                ->where(['account_type' => $request->post('account_type'),'order_id'=>$request->post('order_id')])
                ->asArray()->all();

            $var=[
                'status'=>1,
                'msg'=>'获取数据成功',
                'data'=>$query,
            ];
        }else{
            $var=[
                'status'=>0,
                'msg'=>'获取数据失败',
            ];
        }
        echo json_encode($var);
    }

    /**
     * 计算一笔订单的总利润
     */
    public function actionGetProfit()
    {
        $request = Yii::$app->request;
        if($request->isAjax) {
            $profits_in = Accounts::find()->select('amount')
                ->where(['account_type' => 1,'order_id'=>$request->post('order_id')])
                ->sum('amount');

            $profits_out = Accounts::find()->select('amount')
                ->where(['account_type' => 2,'order_id'=>$request->post('order_id')])
                ->sum('amount');
            $profit = $profits_in - $profits_out;

            $var=[
                'status'=>1,
                'msg'=>'获取数据成功',
                'data'=>$profit,
            ];
        }else{
            $var=[
                'status'=>0,
                'msg'=>'获取数据失败',
            ];
        }
        echo json_encode($var);

    }


}