<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\widgets\ActiveForm;
$this->title = '修改密码';
AppAsset::register($this);
?>
<style>
    .box{border: none;}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-4"></div>
        <div class="col-xs-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">修改密码</h3>
                </div>
                <div class="box-body">
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                    <?= $form->field($model, 'old_password')->passwordInput() ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <?= $form->field($model, 'password_repeat')->passwordInput() ?>

                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right">修改密码</button>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="col-xs-4"></div>
    </div>
</section>




