<?php
/**
 * Created by PhpStorm.
 * User: macmini
 * Date: 17/3/23
 * Time: 上午11:34
 */

namespace app\controllers;


use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions'=>['before'],
                        'roles' => ['leader'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'query-user' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex(){

    }


    /**
     * 分配权限
     */
    public function actionCreateEmpowerment()
    {
        $item=[
            'name'=>'customer',
            'description'=>4,
        ];
        $auth = \Yii::$app->authManager;
        $reader = $auth->createRole($item['name']);
        $auth->assign($reader, $item['description']);
    }



    public function actionBefore()
    {

//        $action = Yii::$app->controller->action->id;
        if(\Yii::$app->user->can('leader')){
            return true;
        }else{
            throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
        }
    }

}