<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\widgets\ActiveForm;
use \yii\helpers\Html;

$this->title = '订单';


?>

<script type="text/javascript">
    <?php $this->beginBlock('js'); ?>
    jQuery(function($){
        $('.datepicker').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            language: 'zh-CN',
            startDate: "<?=date("Y-m-d H:i")?>",
            autoclose: true,
            todayBtn: true,
            defaultViewDate: '<?=date("Y-m-d")?>'
        });
    });


        $(document).ready(function(){
           $('#ordercreateform-mobile').attr('onkeypress','keyPress()');
           $('#ordercreateform-qq').attr('onkeypress','keyPress()');
           $('#ordercreateform-total_len').attr('onkeypress','keyPress()');
           $('#ordercreateform-amount').attr('onkeypress','keyPress()');
        });

        function keyPress(){
            var keyCode = event.keyCode;
            if ((keyCode >= 48 && keyCode <= 57)) {
                event.returnValue = true;
            }else{
                event.returnValue = false;
            }

        }

    <?php $this->endBlock(); ?>
</script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        <h4><?= $this->title?></h4>
                    </div>

                </div>
                <div class="box-body">
                    <?php $form= ActiveForm::begin()?>
                    <div class="row">
                        <div class="col-sm-2">
                            <?= $form->field($model, 'guest_name') ?>
                            <?= $form->field($model, 'title') ?>
                        </div>
                        <div class="col-sm-2">
                            <?= $form->field($model, 'mobile') ?>
                            <?= $form->field($model, 'qq') ?>
                        </div>
                        <div class="col-sm-2">
                            <?= $form->field($model, 'total_len') ?>
                            <?= $form->field($model, 'amount') ?>
                        </div>
                        <div class="col-sm-2">
                            <?= $form->field($model, 'type')->dropdownList(Yii::$app->params['orders_type'],['prompt'=>'请选择']); ?>
                            <?= $form->field($model, 'appointed_time')->textInput([
                                'class'=>'form-control datepicker',
                                'data-provide'=>'datepicker',
                                'value'=>date("Y-m-d H:i"),
                                'readonly'=>'readonly',
                            ]) ?>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-7">
                            <?= $form->field($model, 'note')->textarea(['rows'=>'12']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= Html::submitButton('添加', ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Html::button('返回',['class'=>'btn btn-info']),Yii::$app->request->referrer)?>
                            </div>
                        </div>
                    </div>
                    <?php ActiveForm::end()?>
                </div>
            </div>
        </div>
    </div>
</section>
