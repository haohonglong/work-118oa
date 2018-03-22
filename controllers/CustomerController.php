<?php
/**
 * Created by PhpStorm.
 * User: haohonglong
 * Date: 17/4/13
 * Time: 下午6:49
 */

namespace app\controllers;


use app\models\Orders;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;

class CustomerController extends Controller
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
    //支持根据选择售前个人名字执行搜索
    public function actionIndex()
    {
        $userInfo = User::getFrontSaleAll();
        $id = \Yii::$app->request->get('id');
        $query = Orders::find();
        $query->innerJoin('user u','orders.uid = u.id')
            ->innerJoin('auth_assignment as auth','u.id = auth.user_id')
            ->where(['and',['orders.workflow'=>8,'orders.status'=>2,'auth.item_name'=>'front_sale']]);

        if (isset($id) && !empty($id)) {
            $query->andWhere(['u.id' => $id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pagesize'=>'20'],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $var['dataProvider'] = $dataProvider;
        $var['userInfo'] = $userInfo;
        return $this->render('index',$var);

    }

}
