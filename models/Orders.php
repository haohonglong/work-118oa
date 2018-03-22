<?php

namespace app\models;

use yii;
use yii\data\Pagination;
use yii\data\SqlDataProvider;

/**
 * This is the model class for table "orders".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $uid
 * @property string $title
 * @property string $guest_name
 * @property string $after_sale
 * @property integer $total_len
 * @property integer $workflow
 * @property double $amount
 * @property double $cost_in
 * @property double $cost_out
 * @property double $profit
 * @property double $finished
 * @property string $appointed_time
 * @property string $publish_time
 * @property string $created_time
 * @property string $update_time
 * @property integer $status
 * @property integer $type
 * @property string $qq
 * @property string $mobile
 * @property string $note
 */
class Orders extends \yii\db\ActiveRecord
{

    public static $time_arr;
    public static $last_work=7;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['guest_name','title','total_len','type','amount'], 'required'],
            [['total_len'], 'integer'],
            [['amount'], 'number'],
            [['note'], 'string'],
            [['order_id'], 'string', 'max' => 16],
            [['title', 'mobile'], 'string', 'max' => 180],
            [['guest_name', 'after_sale'], 'string', 'max' => 32],
            [['qq'], 'string', 'max' => 12],
        ];
    }

    public static function generateOrderId()
    {
        return date('ymdHis').mt_rand(100,999);
    }
    public static function findById($id)
    {
        return static::find()->where(['id'=>$id])->limit(1)->one();
    }

    public static function findByOrderId($order_id)
    {
        return static::find()->where(['order_id'=>$order_id])->limit(1)->one();
    }

    /**
     * 统计所有黄稿
     * @param array $arr
     * @return array|null|yii\db\ActiveRecord
     */
    public static function getAccountListOfLose($arr=[
                                                    'finished'=>null
                                                ])
    {

        $where ="o.`status` = 4";
        //按订单完成时间搜索
        $finished = $arr['finished'];
        if(isset($finished) && !empty($finished)){
            $where.=" AND o.created_time BETWEEN :start AND :end";
        }
        $query = static::find();
        $query->from(['o'=>static::tableName()])
            ->select('COUNT(o.id) as num,sum(o.amount) as amount,sum(o.profit) as profit')
            ->asArray()
            ->where($where);
        if(isset($finished) && !empty($finished)){
            $params = [
                ':start' => $finished[0],
                ':end'   => $finished[1]
            ];
            $query->addParams($params);
        }
        $all = $query->one();
        return $all;
    }

    /**
     * 由订单类型统计及黄稿统计导出excel
     * @param $report_name
     * @param $data
     */
    public static function getAccountListByTypeExportExcel($report_name,$data)
    {
        $finished = $data['finished'];
        $list = $data['list'];
        $total = $data['total'];
        $yellow = isset($data['yellow_all']) ? $data['yellow_all'] : null;
        $filename=$report_name.'_'.date('ymd');
        $objPHPExcel = new \PHPExcel();

// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '日期')
            ->setCellValue('A2', '业务类型')
            ->setCellValue('B2', '单量')
            ->setCellValue('C2', '订单金额')
            ->setCellValue('D2', '字数')
            ->setCellValue('E2', '入账金额')
            ->setCellValue('F2', '出账金额')
            ->setCellValue('G2', '利润');

// Miscellaneous glyphs, UTF-8
        $sheeet = $objPHPExcel->setActiveSheetIndex(0);
        $sheeet->setCellValue('B1', $finished);
        $i=3;
        foreach ($list as $k=>$v){
            $sheeet->setCellValue('A'.$i, $v['type']);
            $sheeet->setCellValue('B'.$i, $v['num']);
            $sheeet->setCellValue('C'.$i, $v['amount']);
            $sheeet->setCellValue('D'.$i, $v['total_len']);
            $sheeet->setCellValue('E'.$i, $v['cost_in']);
            $sheeet->setCellValue('F'.$i, $v['cost_out']);
            $sheeet->setCellValue('G'.$i, $v['profit']);
            $i++;
        }
        $sheeet->setCellValue('A'.$i, '总计:');
        $sheeet->setCellValue('B'.$i, $total['num']);
        $sheeet->setCellValue('C'.$i, $total['amount']);
        $sheeet->setCellValue('D'.$i, $total['total_len']);
        $sheeet->setCellValue('E'.$i, $total['cost_in']);
        $sheeet->setCellValue('F'.$i, $total['cost_out']);
        $sheeet->setCellValue('G'.$i, $total['profit']);
        $i+=2;
        if(isset($yellow)){
            $sheeet->setCellValue('A'.$i, '黄稿统计:');
            $i++;
            $sheeet->setCellValue('A'.$i, '单量');
            $sheeet->setCellValue('B'.$i, '订单金额');
            $sheeet->setCellValue('C'.$i, '利润');
            $i++;
            $sheeet->setCellValue('A'.$i, $yellow['num']);
            $sheeet->setCellValue('B'.$i, $yellow['amount']);
            $sheeet->setCellValue('C'.$i, $yellow['profit']);
        }


// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($report_name);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * 由订单类型统计
     * @param array $arr
     * @return array|yii\db\ActiveRecord[]
     */
    public static function getAccountListByType(
                                                $arr=[
                                                    'type'=>null,
                                                    'status'=>2,
                                                    'finished'=>null,
                                                ]
    )
    {
        $total =[
            'num'=>0,
            'amount'=>0,
            'cost_in'=>0,
            'cost_out'=>0,
            'profit'=>0,
            'total_len'=>0,
        ];
        $type   = isset($arr['type']) ? $arr['type'] : null ;
        $status = isset($arr['status']) ? $arr['status'] : 2 ;
        $where ="o.`status` = {$status}";
        if($type){
            $where.= " and o.type = {$type}";
        }

        //按订单创建时间搜索
        $finished = $arr['finished'];
        if(isset($finished) && !empty($finished)){
            $where.=" AND o.created_time BETWEEN :start AND :end";
        }

        $sql = "select COUNT(o.id) as num,o.type,sum(o.amount) as amount,sum(o.total_len) as total_len,sum(o.cost_in) as cost_in,sum(o.cost_out) as cost_out,sum(o.profit) as profit from orders as o
where o.status = 2
group by o.type";

        $query = static::find();
        $query->from(['o'=>static::tableName()])
            ->select('COUNT(o.id) as num,o.type,sum(o.amount) as amount,sum(o.total_len) as total_len,sum(o.cost_in) as cost_in,sum(o.cost_out) as cost_out,sum(o.profit) as profit')
            ->asArray()
            ->where($where);

        if(isset($finished) && !empty($finished)){
            $params = [
                ':start' => $finished[0],
                ':end'   => $finished[1]
            ];
            $query->addParams($params);
        }
        $all = $query->groupBy('o.type')->all();

        foreach ($all as $v){
            $total['num'] += $v['num'];
            $total['amount'] += $v['amount'];
            $total['cost_in'] += $v['cost_in'];
            $total['cost_out'] += $v['cost_out'];
            $total['profit'] += $v['profit'];
            $total['total_len'] += $v['total_len'];
        }
        $var['list'] = $all;
        $var['total'] = $total;

        return $var;
    }

    /**
     * 获取订单里的所有账单,订单完成或进行中才可显示
     * @param null $account_type 显示出账还是入账 不填,全部显示
     * @param array $search
     * @return mixed
     */
    public static function getAccountList(
                                            $account_type=null,
                                            $search=[
                                                'order_id'=>null,
                                                'title'=>null,
                                                'pay_time'=>null,
                                                'in_name'=>null,
                                            ]
                                        )
    {

        $arr =[];
        $query = static::find();
        $where ="a.check_status = 3 AND (o.`status` = 2 OR o.`status` = 1)";
        if(isset($account_type) && (1 ===$account_type || 2 === $account_type)){
            $where.= " and account_type = {$account_type}";
        }

        $query->from(['o'=>static::tableName()])
            ->select('a.id,a.order_id,a.amount,a.pay_type,a.pay_time,a.serial_number,a.check_status,a.in_address,a.in_account_number,a.in_name,a.account_type,a.note')
            ->addSelect('o.title')
            ->innerJoin('accounts as a','a.order_id = o.order_id')
//            ->indexBy('order_id')
            ->asArray()
            ->where($where);

        //按订单号搜索
        if(isset($search['order_id']) && !empty($search['order_id'])){
            $query->andWhere(['a.order_id'=>$search['order_id']]);
        }
        //按客户名称搜索
        if(isset($search['in_name']) && !empty($search['in_name'])){
            $query->andWhere(['like','a.in_name',$search['in_name']]);
        }

        if(isset($search['title']) && !empty($search['title'])){
            $query->andWhere(['like','o.title',$search['title']]);
        }

        //转账时间搜索
        if(isset($search['pay_time']) && !empty($search['pay_time'])){
            $start = $search['pay_time'][0];
            $end   = $search['pay_time'][1];
            $query->andWhere(['between','a.pay_time',$start,$end]);
        }

        $all = $query->groupBy('a.id')->all();

        foreach ($all as $k => $v){
            $arr[$v['order_id']][] = $v;
        }

        $var['list']=$arr;
        $var['query']=$all;

        return $var;
    }


    /**
     *导出excle 给写手看的
     * @param $report_name
     * @param $data {Array} static::getAccountList($account_type=null,$search) 返回的数据
     */
    public static function exportExcleWriter($report_name,$data)
    {
        $list = $data['query'];
        $filename=$report_name.'_'.date('ymd');
        $objPHPExcel = new \PHPExcel();

// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
//            ->setCellValue('A1', '订单号')
            ->setCellValue('A1', '收款人名字')
            ->setCellValue('B1', '题目')
            ->setCellValue('C1', '转账方式')
            ->setCellValue('D1', '转账时间')
            ->setCellValue('E1', '金额')
            ->setCellValue('F1', '备注');

// Miscellaneous glyphs, UTF-8
        $sheeet = $objPHPExcel->setActiveSheetIndex(0);
        $i=2;
        foreach ($list as $k=>$v){
//            $sheeet->setCellValue('A'.$i, $v['order_id'].' ');
            $sheeet->setCellValue('A'.$i, $v['in_name']);
            $sheeet->setCellValue('B'.$i, $v['title']);
            $sheeet->setCellValue('C'.$i, $v['pay_type']);
            $sheeet->setCellValue('D'.$i, $v['pay_time']);
            $sheeet->setCellValue('E'.$i, $v['amount']);
            $sheeet->setCellValue('F'.$i, $v['note']);
            $i++;
        }

// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($report_name);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }




    /**
     * 更新入账或出账金额,且计算利润
     * @param $order_id
     * @return bool
     */
    public static function updateCost($order_id)
    {
        $query = Accounts::find()->where(['order_id'=>$order_id,'check_status'=>3])->all();
        $cost_in = $cost_out = 0;
        foreach ($query as $item){
            if(1 === $item->account_type){
                $cost_in  += $item->amount;
            }else if(2 === $item->account_type){
                $cost_out += $item->amount;
            }
        }
        $order = static::find()->where(['order_id'=>$order_id])->limit(1)->one();
        $order->cost_in  = $cost_in;
        $order->cost_out = $cost_out;
        $order->profit   = $cost_in - $cost_out;
        if($order->save()){
            return true;
        }
        return false;
    }

    /**
     * @param array $arr
     * @return array
     */
    public static function getAllOrderReport($arr=[
        'finished'=>null,
        'uid'=> -1,
        'pagination'=>null,
    ],$orderStatus=0)
    {
        $where = " a.check_status = 3 and auth.item_name = 'front_sale' and o.status<>3";
        if(isset($arr['uid']) && $arr['uid'] > 0 ){
            $where.=" AND o.uid ={$arr['uid']}";
        }
        //按订单完成时间搜索
        $finished = $arr['finished'];
        //按订单完成时间搜索
        if(isset($finished) && is_array($finished)){
            if(2 == $orderStatus){
                $where.=" AND finished BETWEEN :start AND :end and o.status=2";
            }else{
                $where.=" AND created_time BETWEEN :start AND :end and o.status<>3";
            }

        }


        $sql ="select u.real_name,o.order_id,o.title,o.amount,o.type,o.total_len,o.cost_in,o.cost_out,o.profit,ROUND(o.profit/o.cost_in*100) as per from orders as o
inner join accounts as a on a.order_id = o.order_id
inner join user as u on u.id = o.uid
inner join auth_assignment as auth on auth.user_id = u.id
where {$where}
GROUP BY o.order_id
ORDER BY u.id";

        $query = Yii::$app->db->createCommand($sql);
        if(isset($finished) && is_array($finished)){
            $params = [
                ':start' => $finished[0],
                ':end'   => $finished[1]
            ];
            $query->bindValues($params);
        }

        $sql = $query->getRawSql();
        $query = $query->queryAll();
        //echo $count;
        if(isset($arr['pagination']) && $arr['pagination']){
            $count = sizeof($query);
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'totalCount' => $count,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
            $var =[
                'models' => $dataProvider->getModels(),
                'dataProvider' => $dataProvider,
            ];
        }else{
            $var =[
                'models' =>$query
            ];
        }
        return $var;
    }

    /**
     * 获取报表数据
     * @param array $finished
     * @return array
     */
    public static function getReportForm($finished=[],$orderStatus=0)
    {
        $db =Yii::$app->db;
        $where = " auth.item_name='front_sale' and o.status<>3";
        //按订单完成时间搜索
        if(isset($finished) && is_array($finished)){
            if(2 == $orderStatus){
                $where.=" AND finished BETWEEN :start AND :end and o.status=2 and o.workflow = 8";
            }else{
                $where.=" AND created_time BETWEEN :start AND :end and o.status <>3";
            }

        }

        $sql=<<<SQL
        SELECT u.id,u.username,u.real_name,sum(o.profit) as profit,sum(cost_in+cost_out) as sales,sum(o.total_len) as type_total,sum(cost_in) as cost_in,sum(cost_out) as cost_out,sum(amount) as orderAmount
        FROM user AS u
            INNER JOIN orders as o ON o.uid = u.id
            INNER JOIN auth_assignment as auth ON auth.user_id=o.uid
            WHERE {$where}
            GROUP BY o.uid
SQL;

        $query = static::find();
        $query
            ->from(['o'=>static::tableName()])
            ->select('u.id')
            ->innerJoin('user u','u.id=o.uid')
            ->innerJoin('auth_assignment as auth','auth.user_id=o.uid')
            ->where($where)
            ->groupBy('u.id');

        $sql2="SELECT sum(o.profit) as profit,sum(cost_in+cost_out) as sales,sum(o.total_len) as type_total,sum(cost_in) as cost_in,sum(cost_out) as cost_out,sum(amount) as orderAmount
        FROM user AS u
            INNER JOIN orders as o ON o.uid = u.id
            INNER JOIN auth_assignment as auth ON auth.user_id=o.uid
            WHERE {$where}";

        $query2 = $db->createCommand($sql2);

        $SqlDataProvider_params= [
            'sql' => $sql,
            'pagination' => [
                'pageSize' => 20,
            ],
        ];
        if(isset($finished) && is_array($finished)){
            $arr = [
                ':start' => $finished[0],
                ':end'   => $finished[1]
            ];
            static::$time_arr = $arr;
            $SqlDataProvider_params['params'] = $arr;
            $query->addParams($arr);
            $query2->bindValues($arr);
        }


        $SqlDataProvider_params['totalCount'] = $query->count();

        return [
            'SqlDataProvider_params'=>$SqlDataProvider_params,
            'sum_arr'=>$query2->queryOne(),
        ];

    }



    /**
     *导出excle
     * @param $report_name
     * @param $data static::getReportForm($time_arr) 返回的数据
     */
    public static function exportExcle($report_name,$data)
    {

        $sum_arr = $data['sum_arr'];
        $model =$data['model'];
        $filename=$report_name.'_'.date('ymd');
        $objPHPExcel = new \PHPExcel();


        // Set document properties
//        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
//            ->setLastModifiedBy("Maarten Balliauw")
//            ->setTitle("Office 2007 XLSX Test Document")
//            ->setSubject("Office 2007 XLSX Test Document")
//            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//            ->setKeywords("office 2007 openxml php")
//            ->setCategory("Test result file");

// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '售前')
            ->setCellValue('B1', '总字数')
            ->setCellValue('C1', '入账金额')
            ->setCellValue('D1', '出账金额')
            ->setCellValue('E1', '订单金额')
            ->setCellValue('F1', '总利润')
            ->setCellValue('G1', '百分比');

// Miscellaneous glyphs, UTF-8
        $sheeet = $objPHPExcel->setActiveSheetIndex(0);
        $i=2;
        foreach ($model as $k=>$v){
            $sheeet->setCellValue('A'.$i, $v['real_name']);
            $sheeet->setCellValue('B'.$i, $v['type_total']);
            $sheeet->setCellValue('C'.$i, $v['cost_in']);
            $sheeet->setCellValue('D'.$i, $v['cost_out']);
            $sheeet->setCellValue('E'.$i, $v['orderAmount']);
            $sheeet->setCellValue('F'.$i, $v['profit']);
            if($v['cost_in'] != 0){
                $per = round($v['profit']/$v['cost_in']*100);
                if($per<=45){
                    $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->getColor()->setRGB('ff0000');
                }elseif($per<=50){
                    $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->getColor()->setRGB('046602');
                }else{

                }

                $per.='%';
            }else{
                $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->getColor()->setRGB('ff0000');
                $per =0;
            }
            $sheeet->setCellValue('G'.$i, $per);
            $i++;
        }
        $sheeet->setCellValue('A'.$i, '总计:');
        $sheeet->setCellValue('B'.$i, $sum_arr['type_total']);
        $sheeet->setCellValue('C'.$i, $sum_arr['cost_in']);
        $sheeet->setCellValue('D'.$i, $sum_arr['cost_out']);
        $sheeet->setCellValue('E'.$i, $sum_arr['orderAmount']);
        $sheeet->setCellValue('F'.$i, $sum_arr['profit']);

// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($report_name);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * @param $report_name
     * @param $data
     */
    public static function exportExcleAllOrder($report_name,$data)
    {

        $list = $data['models'];
        $filename=$report_name.'_'.date('ymd');
        $objPHPExcel = new \PHPExcel();

// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '售前')
            ->setCellValue('B1', '订单号')
            ->setCellValue('C1', '题目')
            ->setCellValue('D1', '业务类型')
            ->setCellValue('E1', '字数')
            ->setCellValue('F1', '入账金额')
            ->setCellValue('G1', '出账金额')
            ->setCellValue('H1', '订单金额')
            ->setCellValue('I1', '利润')
            ->setCellValue('J1', '百分比');

// Miscellaneous glyphs, UTF-8
        $sheeet = $objPHPExcel->setActiveSheetIndex(0);
        $i=2;
        foreach ($list as $k=>$v){
            $sheeet->setCellValue('A'.$i, $v['real_name']);
            $sheeet->setCellValue('B'.$i, $v['order_id'].' ');
            $sheeet->setCellValue('C'.$i, $v['title']);
            $sheeet->setCellValue('D'.$i, $v['type']);
            $sheeet->setCellValue('E'.$i, $v['total_len']);
            $sheeet->setCellValue('F'.$i, $v['cost_in']);
            $sheeet->setCellValue('G'.$i, $v['cost_out']);
            $sheeet->setCellValue('H'.$i, $v['amount']);
            $sheeet->setCellValue('I'.$i, $v['profit']);
            if($v['cost_in'] != 0){
                $per = $v['per'];
                if($per<=45){
                    $objPHPExcel->getActiveSheet()->getStyle(''.$i)->getFont()->getColor()->setRGB('ff0000');
                }elseif($per<=50){
                    $objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->getColor()->setRGB('046602');
                }else{

                }

                $per.='%';
            }else{
                $objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->getColor()->setRGB('ff0000');
                $per =0;
            }
            $sheeet->setCellValue('J'.$i, $per);
            $i++;
        }

// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($report_name);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }







    public function getUser()
    {
        //同样第一个参数指定关联的子表模型类名
        //
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    public function getSale()
    {
        return $this->hasOne(User::className(), ['username' => 'after_sale']);
    }





    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单号',
            'title' => '论文标题',
            'guest_name' => '客户名称',
            'after_sale' => '售后客服',
            'total_len' => '总字数',
            'amount' => '订单金额',
            'cost_in' => '入账金额',
            'cost_out' => '出账金额',
            'profit' => '利润',
            'appointed_time' => '约定时间',
            'publish_time' => '写手交稿时间',
            'finished' => '订单完成时间',
            'type' => '类型',
            'qq' => 'QQ',
            'mobile' => '联系方式',
            'created_time' => '创建时间',
            'workflow' => '工作流',
            'update_time' => '更新时间',
            'status' => '状态',
            'note' => '订单备注',
        ];
    }
}
