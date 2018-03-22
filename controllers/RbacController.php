<?php
/**
 * Created by PhpStorm.
 * User: haohonglong
 * Date: 17/4/1
 * Time: 下午6:04
 */

namespace app\controllers;


use yii\web\Controller;

class RbacController extends Controller
{
    public function actionAdd()
    {
        $auth = \Yii::$app->authManager;

        $p_leader = $auth->createPermission('p_leader');
        $p_leader->description = '主管 权限';

    }

    public function actionInit()
    {
        $auth = \Yii::$app->authManager;

        $p_admin = $auth->createPermission('p_admin');
        $p_admin->description = '管理员 权限';
        $auth->add($p_admin);

        $p_leader = $auth->createPermission('p_leader');
        $p_leader->description = '主管 权限';
        $auth->add($p_leader);

        $p_accounter = $auth->createPermission('p_accounter');
        $p_accounter->description = '财务 权限';
        $auth->add($p_accounter);

        $p_front_sale = $auth->createPermission('p_front_sale');
        $p_front_sale->description = '售前 权限';
        $auth->add($p_front_sale);

        $p_after_sale = $auth->createPermission('p_after_sale');
        $p_after_sale->description = '售后 权限';
        $auth->add($p_after_sale);


        $admin = $auth->createRole('admin');
        $admin->description = '管理员';
        $auth->add($admin);

        $account = $auth->createRole('accounter');
        $account->description = '财务';
        $auth->add($account);

        $leader = $auth->createRole('leader');
        $leader->description = '主管';
        $auth->add($leader);

        $after_sale = $auth->createRole('after_sale');
        $after_sale->description = '售后';
        $auth->add($after_sale);

        $front_sale = $auth->createRole('front_sale');
        $front_sale->description = '售前';
        $auth->add($front_sale);


        $auth->addChild($admin,$p_admin);
        $auth->addChild($leader,$p_leader);
        $auth->addChild($account,$p_accounter);
        $auth->addChild($after_sale,$p_after_sale);
        $auth->addChild($front_sale,$p_front_sale);

        $auth->addChild($admin,$leader);
        $auth->addChild($admin,$account);

        $auth->addChild($leader,$front_sale);
        $auth->addChild($leader,$after_sale);


        $auth->assign($admin,1);
        $auth->assign($leader,6);
        $auth->assign($front_sale,2);
        $auth->assign($front_sale,7);
        $auth->assign($front_sale,8);
        $auth->assign($front_sale,9);
        $auth->assign($after_sale,5);
        $auth->assign($after_sale,4);
        $auth->assign($account,3);
    }
}