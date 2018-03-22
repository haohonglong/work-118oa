<?php
/**
 * Created by PhpStorm.
 * User: lhh
 * Date: 17/3/23
 * Time: 下午1:17
 */

namespace app\controllers;

use app\helpers\Helper;
use app\models\Accounts;
use app\models\AccountsInCreateForm;
use app\models\AccountsInEditForm;
use app\models\AccountsOutCreateForm;
use app\models\AccountsOutEditForm;
use app\models\Files;
use app\models\Logs;
use app\models\Message;
use app\models\OrderCreateForm;
use app\models\OrderEditForm;
use app\models\Orders;
use app\models\UploadForm;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii;
use yii\db\Query;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;


class OrderController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' =>[
                    ['allow' => true,'roles' => ['@']]
                ]
            ],
        ];
    }
    private function showMessage($type)
    {
        $message = Message::show($type);
        return $message;
    }

    public function actionIndex()
    {
        $message_arr =[];
        $request = Yii::$app->request;
        $query = Orders::find();
        $qq = trim($request->get('qq'));
        $order_id = trim($request->get('order_id'));
        $after_sale = trim($request->get('after_sale'));
        $front_sale_id = trim($request->get('front_sale_id'));
        $guest_name = trim($request->get('guest_name'));
        $title = trim($request->get('title'));
        $type = trim($request->get('type'));
        $update_time = Helper::daterangeToArray(trim($request->get('update_time')));
        $publish_time = trim($request->get('publish_time'));
        $workflow = trim($request->get('workflow'));
        $orders_status = trim($request->get('orders_status'));



        //按订单号搜索
        if(isset($order_id) && !empty($order_id)){
            $query->andWhere(['order_id'=>$order_id]);
        }
        //按QQ号搜索
        if(isset($qq) && !empty($qq)){
            $query->andWhere(['qq'=>$qq]);
        }

        //按工作流
        if(isset($workflow) && !empty($workflow) && $workflow !=0){
            $query->andWhere(['workflow'=>$workflow]);
        }
        //按订单状态
        if(isset($orders_status) && !empty($orders_status) && $orders_status !=0){
            $query->andWhere(['status'=>$orders_status]);
        }
        //按售后名称
        if(isset($after_sale) && !empty($after_sale)){
            $query->andWhere(['after_sale'=>$after_sale]);
        }

        //按售前名称
        if(isset($front_sale_id) && !empty($front_sale_id)){
            $query->andWhere(['uid'=>$front_sale_id]);
        }

        //按客户名称搜索
        if(isset($guest_name) && !empty($guest_name)){
            $query->andWhere(['like','guest_name',$guest_name]);
        }

        //按期刊名搜索
        if(isset($title) && !empty($title)){
            $query->andWhere(['like','title',$title]);
        }


        //按业务类型搜索
        if(isset($type) && !empty($type)){
            $query->andWhere(['like','type',$type]);
        }

        //按更新时间搜索
        if(isset($update_time) && !empty($update_time) && is_array($update_time)){
            $query->andWhere(['between','update_time',$update_time[0],$update_time[1]]);
        }

        //按写手交稿时间搜索
        if(isset($publish_time) && !empty($publish_time)){
            $publish_time_start = $publish_time.' 00:00:00';
            $publish_time_end   = $publish_time.' 23:59:59';
            $query->andWhere(['between','publish_time',$publish_time_start,$publish_time_end]);
        }



        if(!\Yii::$app->user->can('p_after_sale') && !\Yii::$app->user->can('p_accounter')){//只有售前才能看到自己的订单
            $query->andWhere(['uid'=>Yii::$app->user->identity->id]);
        }

        if(\Yii::$app->user->can('p_admin')){
            $message_arr = $this->showMessage(null);
        }elseif(\Yii::$app->user->can('p_after_sale')){
            $message_arr = $this->showMessage(1);
        }elseif(\Yii::$app->user->can('p_accounter')){
            $message_arr = $this->showMessage(2);
        }


        $this->view->params['messages']= $message_arr;
        $var['front_sale'] = User::getFrontSaleAll();
        $var['after_sale'] = User::getAfterSaleAll();
        $var['dataProvider'] = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pagesize'=>'10'],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        return $this->render('index',$var);
    }


    public function actionCreate()
    {
        $model=new OrderCreateForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            $var['title']= '订单创建成功';
            $var['info']= '
            <div class="form-group">
                <a href="'.Url::toRoute(['order/edit','id'=>$model->lastid]).'" class="btn btn-success">添加转账</a>
                <a href="'.Url::toRoute(['order/index']).'" class="btn btn-primary">返回列表</a>
            </div>';

            return $this->render('success',$var);
        }else{
            $var['model']=$model;
            return $this->render('create',$var);
        }
    }

    /**
     * 订单详细数据
     * @param $id
     * @param $model
     * @return mixed
     */
    private function getOrderDetail($id,$model)
    {
        $where = ['id'=>$id];
        if(strlen((string)$id) > 11){
            $where = ['order_id'=>$id];
        }
        $model_edit = Orders::find()->where($where)->limit(1)->one();
//        $model->load($model_edit->toArray(),'');
        $model->order_id = $model_edit->order_id;
        $model->workflow = $model_edit->workflow;
        $model->guest_name = $model_edit->guest_name;
        $model->mobile = $model_edit->mobile;
        $model->total_len = $model_edit->total_len;
        $model->type = $model_edit->type;
        $model->appointed_time = $model_edit->appointed_time;
        $model->title = $model_edit->title;
        $model->qq = $model_edit->qq;
        $model->amount = $model_edit->amount;
        $model->publish_time = $model_edit->publish_time;
        $model->after_sale = $model_edit->after_sale;
        $model->profit = $model_edit->profit;
        $model->note = $model_edit->note;

//        $after_sale =User::getRealName($model->after_sale);
//        if($after_sale){
//            $model->after_sale = $after_sale;
//        }

        //获取上传的文件
        $filelist = Files::find()->select('id,title,filename,path,created_time,type,valid')->orderBy('created_time DESC')->where(['order_id'=>$model->order_id])->asArray()->all();

        $var['after_sales']=User::getAfterSaleAll();
        $var['model']=$model;
        $var['oder_id']=$id;
        $var['workflow']=Yii::$app->params['orders_workflow'][$model->workflow];
        $var['accountInModel']=new AccountsInCreateForm();
        $var['accountOutModel']=new AccountsOutCreateForm();
        $var['accountOutEditModel']=new AccountsOutEditForm();
        $var['accountInEditModel']=new AccountsInEditForm();
        $var['uploadFileModel'] = new UploadForm();
        $var['filelist'] = $filelist;
        $var['accounts']=Accounts::find()->asArray()->orderBy('create_time')->all();
        return $var;
    }


    /**
     * 编辑订单
     * @return string|yii\web\Response
     */
    public function actionEdit()
    {
        if(Yii::$app->request->get('order_id')){
            $id = Orders::findByOrderId(Yii::$app->request->get('order_id'))->id;
        }elseif(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $_GET['id'];
        }
        $model = new OrderEditForm();
        if(Yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) && $model->save($id)){
                $status = $model->status;
                if(\Yii::$app->user->can('p_after_sale') || \Yii::$app->user->can('p_accounter')){
                    //订单状态 ：不是进行中就跳到首页
                    if(1 == $status){
                        return $this->refresh();
                    }else{
                        return $this->actionIndex();
                    }

                }

            }
        }
        $var = $this->getOrderDetail($id,$model);
        return $this->render('edit',$var);
    }

    /**
     * 查看订单
     * @return string
     */
    public function  actionView()
    {
        $id = Orders::findByOrderId(Yii::$app->request->get('order_id'))->id;
        $model = new OrderEditForm();
        $var = $this->getOrderDetail($id,$model);
        return $this->render('view',$var);
    }

    /**
     * 取消订单
     * @param $id
     * @return string|yii\web\Response
     */
    public function actionCancel($id)
    {
        $puery = Orders::findOne($id);
        $puery->status=3;
        if($puery->save(false)){
            Logs::createLog($puery->order_id,'把订单已取消');
            return $this->actionIndex();

        }

    }

    /**
     * 下载文件纪录进日志
     */
    public function actionDownFileLog()
    {
        if(Yii::$app->request->isAjax){
            $order_id = Yii::$app->request->post('order_id');
            $name = Yii::$app->request->post('name');
            $msg='';
            Logs::createLog($order_id,'拒绝成功','操作');

        }
    }

    /**
     *读取提示信息
     */
    public function actionReadMessage()
    {
        $request = Yii::$app->request;
        if($request->isGet){
           $id = $request->get('id');
           $order_id = $request->get('order_id');
           $order = Orders::findByOrderId($order_id);
           if(Message::read($id)){
               $this->redirect(Url::toRoute(['order/edit', 'id' => $order->id]));
           }

        }
    }

    /**
     * 获取显示订单里所有出账,给写手看的 搜索参数
     * @return array
     */
    private function getAccountListOfOrdersData()
    {
        $request = Yii::$app->request;
        $order_id = trim($request->get('order_id'));
        $name = trim($request->get('in_name'));
        $title = trim($request->get('title'));
        $pay_time = trim($request->get('pay_time'));
        $pay_time = Helper::daterangeToArray($pay_time);
        $search = [
            'order_id'=>$order_id,
            'title'=>$title,
            'pay_time'=>$pay_time,
            'in_name'=>$name,
        ];
        return Orders::getAccountList(2,$search);
    }

    /**
     * 显示订单里所有出账,给写手看的
     */
    public function actionAccountListOfOrders(){
        $var = $this->getAccountListOfOrdersData();
        return $this->render('writer',$var);
    }

    /**
     * 显示订单里所有出账,给写手看的 导出excle
     */
    public function actionAccountListOfOrdersExport()
    {
        $var = $this->getAccountListOfOrdersData();
        Orders::exportExcleWriter('报表',$var);

    }

    public function actionReadAll()
    {
        $type = \Yii::$app->request->post('type');
        if(in_array($type,['0','1','2']) && is_numeric($type)){
            if($type >0){
                $updateParam = ['status'=>0,'type'=>$type];
            }else{
                $updateParam = ['status'=>0];
            }
            $res = Message::updateAll(['status'=>1,'update_time'=>date('Y-m-d H:i:s')],$updateParam);
            if($res){
                $rep = ['status'=>0,'msg'=>'一键已读成功'];
            }else{
                $rep = ['status'=>1,'msg'=>'一键已读失败'];
            }
            echo json_encode($rep);
        }
    }

    public function actionEditNote(){
        $request = Yii::$app->request;
        if($request->isPost){
            $order_id = trim($request->post('order_id'));
            $note = trim($request->post('note'));
            $order = Orders::find()->where(['order_id'=>$order_id])->limit(1)->one();
            $order->note = $note;
            if($order->save(false)){
                Logs::createLog($order_id,'修改了订单备注');
            }
            return $this->redirect(Url::to(['order/view','order_id'=>$order_id]));
        }
    }

    public function actionBatchDone(){
        $request = Yii::$app->request;
        if($request->isAjax){
            $orderIds = [];
            $orderId = $request->post('orderId');
            $orders = Orders::find()->select('order_id,workflow,status')->where(['order_id'=>explode(',',$orderId),'workflow'=>8,'status'=>1])->all();
            foreach ($orders as $item){
                $query = (new Query())->from('accounts')->select('order_id,check_status')->where(['order_id'=>$item->order_id,'check_status'=>1])->all();
                if(0 === count($query)){//检测是否有未审核的
                    $account = (new Query())->from('accounts')->select('order_id,check_status')->where(['order_id'=>$item->order_id,'check_status'=>3])->all();
                    if(count($account) > 0){//确保最少有一单出账成功的
                        $orderIds[] = $item->order_id;
                        Logs::createLog($item->order_id,'批量完成');
                    }
                }
            }

            $n = Orders::updateAll(['status'=>2],['in', 'order_id', $orderIds]);
            if($n > 0){
                echo json_encode(['status'=>1]);
            }else{
                echo json_encode(['status'=>0]);
            }


        }

    }








}