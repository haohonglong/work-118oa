<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\grid\GridView;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\bootstrap\ActiveForm;

$this->title = '入帐账单列表';
$this->render('_index');

?>
<script type="text/javascript">
    <?php $this->beginBlock('js'); ?>
    Service.setMenuSelectedStatus('/account/in');

    <?php $this->endBlock(); ?>
</script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3><?= $this->title;?></h3>
                </div>
                <div class="box-body">
                    <?php if(isset($list) && is_array($list)):?>
                    <?php foreach ($list as $items):?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <p class="box-title">
                                        收款人名字:<?= $items[0]['in_name']?>,
                                        转账方式:<?= $items[0]['pay_type']?>,
                                        转账账号:<?php echo !(empty($items[0]['in_account_number'])) ? $items[0]['in_account_number'] : '无';  ?>
                                        总金额: <span class="amount-count"></span>
                                    </p>
                                    <div class="box-tools">
                                        <?php if(!\Yii::$app->user->can('p_admin')):?>
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#accountModal">修改</button>
                                        <?php endif;?>
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th width="100"><input class="account_all" type="checkbox"> 全选</th>
                                            <th width="100">转账金额</th>
                                            <th width="50">订单号</th>
                                            <th width="100">论文标题</th>
                                            <th width="100">转账日期</th>
                                            <th width="">备注</th>
                                        </tr>
                                        </thead>
                                        <tbody class="account-table">
                                        <?php foreach ($items as $item):?>
                                        <tr data-id="<?= $item['id']?>">
                                            <td><input type="checkbox"></td>
                                            <td><?= $item['amount']?></td>
                                            <td><a href="<?=Url::toRoute(['/order/edit', 'id' =>$item['oid']])?>"><?= $item['order_id']?></a></td>
                                            <td><?= $item['title']?></td>
                                            <td><?= $item['pay_time']?></td>
                                            <td><?= $item['note']?></td>
                                        </tr>
                                        <?php endforeach;?>


                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>
                    <?php endforeach;?>
                    <?php endif;?>

                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="accountModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content content">
            <?php $form= ActiveForm::begin()?>
            <input type="hidden" name="accountsBatchEditForm[ids]" id="accountseditform-ids" value="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">修改入账</h4>
            </div>
            <div class="modal-body form-horizontal">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($accountsBatchEditForm, 'pay_time')->textInput([
                            'class'=>'form-control datepicker',
                            'data-provide'=>'datepicker',
                            'data-date-format'=>'yyyy-mm-dd',
                            'readonly'=>'readonly',
                        ]) ?>
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-5">
                        <?= $form->field($accountsBatchEditForm, 'serial_number') ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-primary">修改</button>
            </div>
            <?php ActiveForm::end();?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
