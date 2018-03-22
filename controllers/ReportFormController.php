<?php
/**
 * Created by PhpStorm.
 * User: haohonglong
 * Date: 17/4/10
 * Time: 下午2:27
 */

namespace app\controllers;

use app\models\Logs;
use yii;
use app\helpers\Helper;
use app\models\Orders;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\SqlDataProvider;
use yii\data\Pagination;




class ReportFormController extends Controller
{
    private $dataProvider,$sum_arr;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' =>[
                    ['allow' => true,'roles' => ['leader','accounter']]
                ]
            ],
        ];
    }

    /**
     * 获取报表数据
     * @return array
     */
    private function getReportData()
    {
        $request = \Yii::$app->request;
        $finished = trim($request->get('finished'));
        $orderStatus = trim($request->get('orders_status'));
        $finished = Helper::daterangeToArray($finished);
        $var = Orders::getReportForm($finished,$orderStatus);
        return $var;
    }

    /**
     * 处理获取后的订单数据
     * @param array $data
     * @return mixed
     */
    private function getDataOfOrders($data=[])
    {
        $order_ids = [];
        $front_sales = [];
        $sum_arr = [
            'total_len'=>0,
            'cost_in'=>0,
            'cost_out'=>0,
            'amount'=>0,
            'profit'=>0,
        ];
        foreach ($data as $v){
            $order_ids[]=$v['order_id'];
            if(!isset($front_sales[$v['uid']])){
                $front_sales[$v['uid']] = [
                    'real_name' =>$v['real_name'],
                    'total_len'=>0,
                    'cost_in'=>0,
                    'cost_out'=>0,
                    'amount'=>0,
                    'profit'=>0,
                    'orders'=>[],
                ];
            }
            $front_sales[$v['uid']]['total_len'] += $v['total_len'];
            $front_sales[$v['uid']]['cost_in']   += $v['cost_in'];
            $front_sales[$v['uid']]['cost_out']  += $v['cost_out'];
            $front_sales[$v['uid']]['amount']    += $v['amount'];
            $front_sales[$v['uid']]['profit']    += $v['profit'];
            $front_sales[$v['uid']]['orders'][]  = $v['order_id'];

            $sum_arr['total_len'] += $v['total_len'];
            $sum_arr['cost_in']   += $v['cost_in'];
            $sum_arr['cost_out']  += $v['cost_out'];
            $sum_arr['amount']    += $v['amount'];
            $sum_arr['profit']    += $v['profit'];
        }

        $var['sum_arr'] = $sum_arr;
        $var['data'] = $front_sales;
        $var['ids'] = $order_ids;

        return $var;
    }


    public function actionIndex()
    {
        $request = \Yii::$app->request;
        $date_status = 1;
        $types = [];
        $time = [];
        if($request->isGet){
            $date_status =$request->get('date_status');
            $types =$request->get('types');
            $time = Helper::daterangeToArray(trim($request->get('time')));
        }

        $query = Orders::find()
            ->select("u.real_name,o.*")
            ->from('orders as o')
            ->innerJoin('user u','u.id=o.uid')
            ->innerJoin('auth_assignment as auth','auth.user_id=o.uid')
            ->where(['auth.item_name'=>'front_sale']);
        if(2 == $date_status){
            $query->andWhere(['or','o.status = 1','o.status = 2']);
            if(isset($time) && is_array($time) && !empty($time)){$query->andWhere(['between', 'o.created_time', $time[0],$time[1]]);}
        }else{
            $query->andWhere('o.status = 2');
            if(isset($time) && is_array($time) && !empty($time)){$query->andWhere(['between', 'o.finished', $time[0],$time[1]]);}
        }
        if(isset($order_ids) && is_array($order_ids) && !empty($order_ids)){$query->andWhere(['in', 'o.order_id', $order_ids]);}
        if(isset($types) && is_array($types) && !empty($types)){$query->andWhere(['in', 'o.type', $types]);}

        $data =  $query->groupBy('o.order_id')->asArray()->all();
        $var = $this->getDataOfOrders($data);
        $var['types'] = $types;
        return $this->render('index',$var);
    }

    public function actionAll()
    {
        $var = $this->getReportData();

        $this->dataProvider = new SqlDataProvider($var['SqlDataProvider_params']);
        $this->sum_arr = $var['sum_arr'];
        $var['dataProvider'] = $this->dataProvider;
        $var['sum_arr'] = $this->sum_arr;

        return $this->render('all',$var);
    }

    public function actionType()
    {
        $request = \Yii::$app->request;
        $finished = trim($request->get('finished'));
        $finished = Helper::daterangeToArray($finished);
        $query = Orders::getAccountListByType(['status'=>2,'finished'=>$finished]);
        $var['list'] = $query['list'];
        $unfinished = Orders::getAccountListByType(['status'=>1,'finished'=>$finished]);
        $var['unfinishedList'] = $unfinished['list'];
        $var['total'] = $query['total'];
        $var['un_total'] = $unfinished['total'];
        $var['yellow_all'] = Orders::getAccountListOfLose(['finished'=>$finished]);
        return $this->render('type',$var);
    }

    /**
     * 给领导看的报表 展示订单已完成
     */
    public function actionExportType()
    {
        $request = \Yii::$app->request;
        $finished = trim($request->get('finished'));
        $finished = Helper::daterangeToArray($finished);
        $query = Orders::getAccountListByType(['status'=>2,'finished'=>$finished]);
        $var['finished'] = isset($finished) ? "{$finished[0]}至{$finished[1]}" : "";
        $var['list'] = $query['list'];
        $var['total'] = $query['total'];
        $var['yellow_all'] = Orders::getAccountListOfLose(['finished'=>$finished]);
        Orders::getAccountListByTypeExportExcel('按类型导出报表已完成',$var);
    }

    /**
     * 给领导看的报表 展示订单未完成
     */
    public function actionExportType2()
    {
        $request = \Yii::$app->request;
        $finished = trim($request->get('finished'));
        $finished = Helper::daterangeToArray($finished);
        $query = Orders::getAccountListByType(['status'=>1,'finished'=>$finished]);
        $var['finished'] = isset($finished) ? "{$finished[0]}至{$finished[1]}" : "";
        $var['list'] = $query['list'];
        $var['total'] = $query['total'];
        Orders::getAccountListByTypeExportExcel('按类型导出报表未完成',$var);
    }




    public function actionExport()
    {
        $data = $this->getReportData();
        $sql =$data['SqlDataProvider_params']['sql'];
        $model =\Yii::$app->db->createCommand($sql,Orders::$time_arr)->queryAll();
        $data['model'] = $model;
        Orders::exportExcle('售前统计报表',$data);

    }

    public function actionExportAllOrder()
    {
        $request = \Yii::$app->request;
        $finished = trim($request->get('finished'));
        $orderStatus = trim($request->get('orders_status'));
        $finished = Helper::daterangeToArray($finished);
        $var = Orders::getAllOrderReport(['finished'=>$finished],$orderStatus);
        Orders::exportExcleAllOrder('售前详细报表',$var);

    }

    public function actionMonthList()
    {
        $query = Orders::find()
            ->select(["DATE_FORMAT(o.finished, '%Y-%m') as m","count(o.`order_id`) as num"])
            ->from('orders as o')
            ->innerJoin('user u','u.id=o.uid')
            ->innerJoin('auth_assignment as auth','auth.user_id=o.uid')
            ->where(['o.status'=>2,'o.check_status'=>0,'auth.item_name'=>'front_sale']);
        $query->andWhere(['>=','o.finished','2018-01']);
        $data =  $query->groupBy('m')->asArray()->all();
        $var['data'] = $data;
        return $this->render('monthList',$var);
    }



    /**
     * 结算客服薪水
     */
    public function actionSettlement()
    {

        $request = \Yii::$app->request;
        $check_status = 0;
        $types = [];
        $time = [];
        if($request->isGet){
            $types =$request->get('types');
            $year =$request->get('year');
            $month =$request->get('month');
            if(isset($year) && $year != 'null' && isset($month) && $month != 'null'){
                $time[0] = "$year-$month-01 00:00:00";
                $time[1] = "$year-$month-31 23:59:59";
            }
            $check_status = 1 == trim($request->get('check_status')) ? 1 : 0;
        }

       $query = Orders::find()
            ->select("u.real_name,o.*")
            ->from('orders as o')
            ->innerJoin('user u','u.id=o.uid')
            ->innerJoin('auth_assignment as auth','auth.user_id=o.uid')
            ->where(['o.status'=>2,'o.check_status'=>$check_status,'auth.item_name'=>'front_sale']);
        if(isset($order_ids) && is_array($order_ids) && !empty($order_ids)){$query->andWhere(['in', 'o.order_id', $order_ids]);}
        if(isset($types) && is_array($types) && !empty($types)){$query->andWhere(['in', 'o.type', $types]);}
        if(isset($time) && is_array($time) && !empty($time)){$query->andWhere(['between', 'o.created_time', $time[0],$time[1]]);}

        $data =  $query->groupBy('o.order_id')->asArray()->all();
        $var = $this->getDataOfOrders($data);
        $var['types'] = $types;
        return $this->render('settlement',$var);
    }

    public function actionShowDetail()
    {
        $request = \Yii::$app->request;
        $orders = $request->post('orders');
        $orders = json_decode(base64_decode($orders),true);
        $data = Orders::find()->where(['in','order_id',$orders])->asArray()->all();
        if($data){
            foreach ($data as $k => $v){
                $data[$k]['per'] = 0 == $v['cost_in']? 0 : round($v['profit']/$v['cost_in']*100);
                $data[$k]['status'] = Yii::$app->params['orders_status'][$v['status']];
            }
            echo json_encode($data);
        }else{
            echo 0;
        }


    }

    /**
     * 结算售前工资状态
     */
    public function actionCheckout()
    {
        $request = \Yii::$app->request;
        $ids = $request->post('ids');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $n = Orders::updateAll(['check_status'=>1],['and',['in', 'order_id', $ids],['status'=>2]]);
            if($n == count($ids)){
                $transaction->commit();
                Logs::createLog(date('ymdHis'),'这些订单结算了:'.implode(',',$ids));
            }else{
                $transaction->rollBack();
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->redirect('/report-form/settlement');
    }

    public function actionExportOfFrontsale()
    {
        $request = \Yii::$app->request;
        $title = $request->post('title');
        $url = $request->post('url');
        $orders = $request->post('orders');
        $orders = json_decode(base64_decode($orders),true);
        $datas = (new Query())
            ->select("u.real_name,o.*")
            ->from('orders as o')
            ->innerJoin('user u','u.id=o.uid')->where(['in','o.order_id',$orders])->all();
        $datas = $this->getDataOfOrders($datas);

        if(false === $datas){
            $this->redirect($url);
        }else{
            $data = $datas['data'];
            $sum_arr = $datas['sum_arr'];
            Helper::exportExcle(function($PHPExcel,$name) use($data,$sum_arr){
                // Add some data
                $PHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '售前')
                    ->setCellValue('B1', '总字数')
                    ->setCellValue('C1', '入账金额')
                    ->setCellValue('D1', '出账金额')
                    ->setCellValue('E1', '订单金额')
                    ->setCellValue('F1', '总利润')
                    ->setCellValue('G1', '百分比');

// Miscellaneous glyphs, UTF-8
                $sheeet = $PHPExcel->setActiveSheetIndex(0);
                $i=2;
                foreach ($data as $k=>$v){
                    $sheeet->setCellValue('A'.$i, $v['real_name']);
                    $sheeet->setCellValue('B'.$i, $v['total_len']);
                    $sheeet->setCellValue('C'.$i, $v['cost_in']);
                    $sheeet->setCellValue('D'.$i, $v['cost_out']);
                    $sheeet->setCellValue('E'.$i, $v['amount']);
                    $sheeet->setCellValue('F'.$i, $v['profit']);
                    if($v['cost_in'] != 0){
                        $per = round($v['profit']/$v['cost_in']*100);
                        if($per<=45){
                            $PHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->getColor()->setRGB('ff0000');
                        }elseif($per<=50){
                            $PHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->getColor()->setRGB('046602');
                        }else{

                        }

                        $per.='%';
                    }else{
                        $PHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->getColor()->setRGB('ff0000');
                        $per =0;
                    }
                    $sheeet->setCellValue('G'.$i, $per);
                    $i++;
                }
                $sheeet->setCellValue('A'.$i, '总计:');
                $sheeet->setCellValue('B'.$i, $sum_arr['total_len']);
                $sheeet->setCellValue('C'.$i, $sum_arr['cost_in']);
                $sheeet->setCellValue('D'.$i, $sum_arr['cost_out']);
                $sheeet->setCellValue('E'.$i, $sum_arr['amount']);
                $sheeet->setCellValue('F'.$i, $sum_arr['profit']);

                // Rename worksheet
                $PHPExcel->getActiveSheet()->setTitle($name);
                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $PHPExcel->setActiveSheetIndex(0);


            },$title);
        }



    }



    public function actionExportOfOrders()
    {
        $request = \Yii::$app->request;
        $title = $request->post('title');
        $url = $request->post('url');
        $orders = $request->post('orders');
        $orders = json_decode(base64_decode($orders),true);
        $data = (new Query())->from('orders')->where(['in','order_id',$orders])->all();
        $users = (new Query())->select('id,real_name')->from('user')->indexBy('id')->all();

        if(false === $data){
            $this->redirect($url);
        }else{
            $orders =[];
            foreach ($data as $v){
                $orders[$v['uid']][]  = [
                    'order_id'=>$v['order_id'],
                    'title'=>$v['title'],
                    'type'=>$v['type'],
                    'total_len'=>$v['total_len'],
                    'cost_in'=>$v['cost_in'],
                    'cost_out'=>$v['cost_out'],
                    'amount'=>$v['amount'],
                    'profit'=>$v['profit'],
                    'per'=>0 == $v['cost_in']? 0 : round($v['profit']/$v['cost_in']*100),
                    'status'=> Yii::$app->params['orders_status'][$v['status']],
                    'created_time'=>$v['created_time'],
                    'finished'=>$v['finished'],
                ];
            }
            $data = $orders;
            Helper::exportExcle(function($PHPExcel,$name) use($data,$users){
                $PHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '售前')
                    ->setCellValue('B1', '订单号')
                    ->setCellValue('C1', '题目')
                    ->setCellValue('D1', '业务类型')
                    ->setCellValue('E1', '字数')
                    ->setCellValue('F1', '入账金额')
                    ->setCellValue('G1', '出账金额')
                    ->setCellValue('H1', '订单金额')
                    ->setCellValue('I1', '利润')
                    ->setCellValue('J1', '百分比')
                    ->setCellValue('K1', '订单状态')
                    ->setCellValue('L1', '创建日期')
                    ->setCellValue('M1', '完成日期');

// Miscellaneous glyphs, UTF-8
                $sheeet = $PHPExcel->setActiveSheetIndex(0);
                $i=2;
                foreach ($data as $k => $item){
                    foreach ($item as $v){
                        $sheeet->setCellValue('A'.$i, $users[$k]['real_name']);
                        $sheeet->setCellValue('B'.$i, $v['order_id'].' ');
                        $sheeet->setCellValue('C'.$i, $v['title']);
                        $sheeet->setCellValue('D'.$i, $v['type']);
                        $sheeet->setCellValue('E'.$i, $v['total_len']);
                        $sheeet->setCellValue('F'.$i, $v['cost_in']);
                        $sheeet->setCellValue('G'.$i, $v['cost_out']);
                        $sheeet->setCellValue('H'.$i, $v['amount']);
                        $sheeet->setCellValue('I'.$i, $v['profit']);
                        $sheeet->setCellValue('K'.$i, $v['status']);
                        $sheeet->setCellValue('L'.$i, $v['created_time']);
                        $sheeet->setCellValue('M'.$i, $v['finished']);
                        if($v['cost_in'] != 0){
                            $per = $v['per'];
                            if($per<=45){
                                $PHPExcel->getActiveSheet()->getStyle(''.$i)->getFont()->getColor()->setRGB('ff0000');
                            }elseif($per<=50){
                                $PHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->getColor()->setRGB('046602');
                            }else{

                            }

                            $per.='%';
                        }else{
                            $PHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->getColor()->setRGB('ff0000');
                            $per =0;
                        }
                        $sheeet->setCellValue('J'.$i, $per);
                        $i++;
                    }

                }

// Rename worksheet
                $PHPExcel->getActiveSheet()->setTitle($name);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $PHPExcel->setActiveSheetIndex(0);


            },$title);
        }



    }


}