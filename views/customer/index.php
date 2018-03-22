<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\grid\GridView;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\bootstrap\ActiveForm;

$this->title = '客服列表';

?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row MB10">
                    <div class="col-sm-12">搜索</div>
                        <div class="col-sm-1">
                        
                        <?php $form= ActiveForm::begin([
                        'method'=>'GET',
                        'options'=>['class'=>'form-inline'],
                        'action'=>Url::toRoute(['/customer/index']),
                        ])?>

                        <select class="form-control" name="id">
                         <option value="0">所有</option>
                          <?php foreach($userInfo as $info): ?>
                          <?php if (isset($_GET['id']) && !empty($_GET['id']) && $info['id'] == $_GET['id']):?>
                            <option value="<?= $info['id'] ?>" selected="selected"><?= $info['real_name'] ?></option>
                          <?php else:?>
                            <option value="<?= $info['id'] ?>" ><?= $info['real_name'] ?></option>
                          <?php endif;?>
                          <?php endforeach;?>
                        </select>
                        </div>
                        <div class="col-sm-1">
                        <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> 查询</button>
                        </div>
                        <?php ActiveForm::end();?>

                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            if (isset($dataProvider)){

                                echo GridView::widget([
                                    'layout' => "{items}{summary}{pager}",
                                    'tableOptions'=>['class' => 'table table-bordered table-hover'],
                                    'dataProvider' => $dataProvider,
                                    'showFooter'=>TRUE,
                                    'rowOptions' => function($model) {
                                        if($model->profit < 0){
                                            return ['class' => 'danger'];
                                        }
                                    },
                                    'columns' => [
                                        [
                                            'headerOptions'=>['width'=>1],
                                            'header'=>'售前',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                return $model->user->real_name;
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>5],
                                            'header'=>'业务明细',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                return $model->title;
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>5],
                                            'header'=>'订单号',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                return $model->order_id;
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>5],
                                            'header'=>'入账金额',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                return $model->cost_in;
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>5],
                                            'header'=>'出账金额',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                return $model->cost_out;
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>5],
                                            'header'=>'利润',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                return '<span class="profit">'.$model->profit.'</span>';
                                            },
                                        ],



                                        [
                                            'headerOptions'=>['width'=>5],
                                            'header'=>'字数',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                return '<span class="total_len">'.$model->total_len.'</span>';

                                            },
                                        ],

                                        [
                                            'headerOptions'=>['width'=>150],
                                            'header'=>'订单完成时间',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                if(isset($model->finished) && !empty($model->finished)){
                                                    return $model->finished;
                                                }
                                                return '';

                                            },
                                        ],

                                    ]
                                ]);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    <?php $this->beginBlock('js'); ?>
    jQuery(function($){
        var total_len=0,profit=0;
        $('.total_len').each(function(){
            total_len+=parseInt($(this).text());
        });
        $('.profit').each(function(){
            profit+=parseFloat($(this).text());
        });

        var html = '\
            <tr><td colspan="8">\
            <div>\
            <span class="MR10">总字数: '+total_len+'</span>\
            <span class="MR10">总利润: '+profit.toFixed(2)+'</span>\
            </div>\
            </td>';
        $('table tfoot').html(html);
    });

    <?php $this->endBlock(); ?>
</script>

<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>
