<?php
/**
 * Created by PhpStorm.
 * User: macmini
 * Date: 17/3/30
 * Time: 下午5:33
 */

namespace app\controllers;

use app\models\Logs;
use yii;
use yii\web\Controller;

class LogController extends Controller
{
    /**
     * 显示所有日志信息
     */
    public function actionShowLogs()
    {
        if(Yii::$app->request->isAjax) {
            $post=Yii::$app->request->post();
            $order_id=$post['order_id'];
            $query = Logs::find()->where(['order_id' => $order_id])
                ->asArray()->orderBy('create_time DESC')->all();
            if($query){
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
    }
}