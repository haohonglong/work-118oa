<?php

/* @var $this \yii\web\View */
/* @var $content string */


use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\widgets\Alert;

AppAsset::register($this);
$auth =\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
$auth =array_pop($auth);

?>


<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="skin-blue fixed">
<?php $this->beginBody() ?>
<div class="wrapper">
    <header class="main-header">
        <a href="javascript:void(0);" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>客服</b>管理系统</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>客服</b>管理系统</span>
        </a>
        <div class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <?php
            if(!empty($this->params['messages']) && (\Yii::$app->user->can('p_accounter') || \Yii::$app->user->can('p_after_sale'))){
                $var['count'] = count($this->params['messages']);
                $var['messages'] = $this->params['messages'];
                echo $this->render('/_common/message',$var);
            }
            ?>
        </div>
    </header>
    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="/images/user2-160x160.jpg" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?= Yii::$app->user->identity->real_name?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> <?= $auth->description ?></a>
                </div>
            </div>
            <ul class="nav sidebar-menu">
                <?php
                $menuItemsLeft[] = ['label' => '订单管理','icon'=>'fa fa-circle-o', 'url' => ['/order/index']];
                
                if(\Yii::$app->user->can('p_leader') || \Yii::$app->user->can('p_accounter')) {
                    $menuItemsLeft[] = ['label' => '账务管理','icon'=>'fa fa-circle-o',
                        'child'=>[
                            ['label' => '出账','icon'=>'fa fa-circle-o', 'url' => ['/account/out']],
//                            ['label' => '入账','icon'=>'fa fa-circle-o', 'url' => ['/account/in']],
                        ]

                    ];
                    $child =[
                        ['label' => '售前列表','icon'=>'fa fa-circle-o', 'url' => ['/customer/index']]
                    ];
                    if(\Yii::$app->user->can('p_leader')) {
                        $child[] = ['label' => '订单所有出账','icon'=>'fa fa-circle-o', 'url' => ['/order/account-list-of-orders']];
                    }
                    $menuItemsLeft[] = ['label' => '客服管理','icon'=>'fa fa-circle-o',
                        'child'=>$child
                    ];
                    $menuItemsLeft[] = ['label' => '报表管理','icon'=>'fa fa-circle-o',
                        'child' =>[
                            ['label' => '统计全部售前','icon'=>'fa fa-circle-o', 'url' => ['/report-form/index']],
//                            ['label' => '所有订单','icon'=>'fa fa-circle-o', 'url' => ['/report-form/all']],
                            ['label' => '按类型','icon'=>'fa fa-circle-o', 'url' => ['/report-form/type']],
                            ['label' => '提成结算','icon'=>'fa fa-circle-o', 'url' => ['/report-form/settlement']],
                            ['label' => '月份统计没结算的订单','icon'=>'fa fa-circle-o', 'url' => ['/report-form/month-list']],
                        ]
                    ];
                }
                $menuItemsLeft[] = ['label' => '修改密码','icon'=>'fa fa-circle-o', 'url' => ['/site/change-password']];
                ?>
                <?php foreach ($menuItemsLeft as $k => $item){?>
                    <?php if(isset($item['child']) && is_array($item['child']) && !empty($item['child'])){?>
                        <li class="treeview">
                            <a href="javascript:void(0);">
                                <i class="<?= $item['icon']?>"></i> <span><?= $item['label']?></span>
                            </a>
                            <ul class="treeview-menu">
                                <?php foreach ($item['child'] as $sub){?>
                                    <li><a href="<?= Url::toRoute($sub['url']);?>"><i class="<?= $sub['icon']?>"></i><?= $sub['label']?></a></li>
                                <?php }?>
                            </ul>
                        </li>
                    <?php }else{?>
                        <li class=""><a href="<?= Url::toRoute($item['url']);?>"><i class="<?= $item['icon']?>"></i><?= $item['label']?></a></li>
                    <?php }?>
                <?php }?>

            </ul>
            <a href="<?=Url::toRoute('/site/logout')?>" data-method="post" class="btn bg-blue btn-block btn-logout">退出登录</a>
        </section>
    </aside>
    <div class="content-wrapper">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


