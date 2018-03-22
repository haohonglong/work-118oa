<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\widgets\ActiveForm;
use \yii\helpers\Html;

$this->title = '操作成功';

?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <div class="box-title">
                        <h4><?= $this->title?></h4>
                    </div>

                </div>
                <div class="box-body">
                    <?= $info?>
                </div>
            </div>
        </div>
    </div>
</section>
