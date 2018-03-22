<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\grid\GridView;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\bootstrap\ActiveForm;

$this->title = '订单列表';

?>
<script type="text/javascript">
    <?php $this->beginBlock('js'); ?>
    jQuery(function($){
        <?php
        if(isset($_GET['update_time'])){
            echo "$('.daterange').val('{$_GET['update_time']}');";
        }else{
            echo "$('.daterange').val('');";
        }
        ?>
        $('.datepicker').datetimepicker({
            format: 'yyyy-mm-dd',
            language: 'zh-CN',
            autoclose: true,
            minView: "month",
            todayBtn: true,
            defaultViewDate: '<?=date("Y-m-d")?>'
        });


        $(window).on('keydown',function(event){
            if(13 === event.keyCode){
                $('form [type=submit]').click();
            }
        });

    });

    $('.read').click(function () {
        if(confirm('确定一键已读?')){
            var type = $(this).data('type');
            $.ajax({
                url: "/order/read-all",
                method:"post",
                data:{type:type},
                dataType: "json",
                success:function(data){
                    if(data.status == 0){
                        alert(data.msg);
                        window.location.reload();
                    }
                },
                error:function(data){
                    alert(data.msg);
                }
            });
        }
    });

    <?php $this->endBlock(); ?>
</script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>
<style type="text/css">
    .box-body{overflow: auto;}
    table.dataTable thead > tr > th{padding-right: 10px;}
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row MB20">
                        <div class="col-sm-12">
                            <?php if(\Yii::$app->user->can('p_front_sale')):?>
                                <a href="<?= Url::toRoute(['order/create'])?>" class="btn btn-primary">新订单</a>
                            <?php endif;?>
                        </div>
                    </div>

                    <?php $form= ActiveForm::begin([
                        'method'=>'GET',
                        'options'=>['class'=>'form-inline'],
                        'action'=>Url::toRoute(['/order/index']),
                    ])?>

                    <div class="row MB20">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>订单号</label>
                                <input type="text" class="form-control" placeholder="订单号" name="order_id" value="<?= isset($_GET['order_id'])? $_GET['order_id']:'';?>">
                            </div>
                            <div class="form-group">
                                <label>QQ号</label>
                                <input type="text" class="form-control" placeholder="QQ号" name="qq" value="<?= isset($_GET['qq'])? $_GET['qq']:'';?>">
                            </div>

                            <div class="form-group">
                                <label>业务类型</label>
                                <input type="text" class="form-control" placeholder="业务类型" name="type" value="<?= isset($_GET['type'])? $_GET['type']:'';?>">
                            </div>
                            <div class="form-group">
                                <label>客户名称</label>
                                <input type="text" class="form-control" placeholder="客户名称" name="guest_name" value="<?= isset($_GET['guest_name'])? $_GET['guest_name']:'';?>">
                            </div>
                            <div class="form-group">
                                <label>论文标题</label>
                                <input type="text" class="form-control" placeholder="论文标题" name="title" value="<?= isset($_GET['title'])? $_GET['title']:'';?>">
                            </div>


                        </div>
                    </div>
                    <div class="row MB20">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>工作流状态</label>
                                <select name="workflow">
                                    <option value="0">所有</option>
                                    <?php foreach (Yii::$app->params['orders_workflow'] as $k=>$v):?>
                                        <?php if(isset($_GET['workflow']) && $_GET['workflow'] == $k):?>
                                            <option value="<?=$k?>" selected="selected"><?=$v?></option>
                                        <?php else:?>
                                            <option value="<?=$k?>"><?=$v?></option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>按售后名</label>
                                <select name="after_sale">
                                    <option value="0">所有</option>
                                    <?php foreach ($after_sale as $item):?>
                                        <?php if(isset($_GET['after_sale']) && $_GET['after_sale'] == $item['username']):?>
                                            <option value="<?=$item['username']?>" selected="selected"><?=$item['real_name']?></option>
                                        <?php else:?>
                                            <option value="<?=$item['username']?>"><?=$item['real_name']?></option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>按售前名</label>
                                <select name="front_sale_id">
                                    <option value="0">所有</option>
                                    <?php foreach ($front_sale as $item):?>
                                        <?php if(isset($_GET['front_sale_id']) && $_GET['front_sale_id'] == $item['id']):?>
                                            <option value="<?=$item['id']?>" selected="selected"><?=$item['real_name']?></option>
                                        <?php else:?>
                                            <option value="<?=$item['id']?>"><?=$item['real_name']?></option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                </select>
                            </div>


                            <div class="form-group">
                                <label>订单状态</label>
                                <select name="orders_status">
                                    <option value="0">所有</option>
                                    <?php foreach (Yii::$app->params['orders_status'] as $k=>$v):?>
                                        <?php if(isset($_GET['orders_status']) && $_GET['orders_status'] == $k):?>
                                            <option value="<?=$k?>" selected="selected"><?=$v?></option>
                                        <?php else:?>
                                            <option value="<?=$k?>"><?=$v?></option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>更新日期</label>
                                <input type="text"
                                       class="form-control daterange"
                                       placeholder="更新日期"
                                       name="update_time"
                                       value=""
                                       readonly="readonly"
                                >
                            </div>
                            <div class="form-group">
                                <label>写手交稿时间</label>
                                <input type="text" style="width: 120px;"
                                       class="form-control datepicker"
                                       placeholder="写手交稿时间"
                                       name="publish_time"
                                       value="<?= isset($_GET['publish_time'])? $_GET['publish_time']:'';?>"
                                       data-provide="datepicker"
                                       data-date-format="yyyy-mm-dd"
                                       readonly="readonly"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-11"></div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> 查询</button>
                            </div>
                        </div>
                    </div>
                    <?php ActiveForm::end();?>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            if (isset($dataProvider)){
                                $columns =[
                                    [
                                        'headerOptions'=>['width'=>5],
                                        'header'=>'订单创建人',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return $model->user->real_name;
                                        },
                                    ],
                                    [
                                        'headerOptions'=>['width'=>1],
                                        'header'=>'订单编号',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return $model->order_id;
                                        },
                                    ],
                                    [
                                        'headerOptions'=>['width'=>80],
                                        'header'=>'QQ号',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return $model->qq;
                                        },
                                    ],

                                    [
                                        'headerOptions'=>['width'=>1],
                                        'header'=>'客户名称',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return $model->guest_name;
                                        },
                                    ],
                                    [
                                        'headerOptions'=>['width'=>250],
                                        'header'=>'论文标题',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return '<div title="'.$model->note.'">'.$model->title.'</div>';
                                        },
                                    ],
                                    [
                                        'headerOptions'=>['width'=>1],
                                        'header'=>'业务类型',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return $model->type;
                                        },
                                    ],
//                                    [
//                                        'headerOptions'=>['width'=>1],
//                                        'header'=>'订单金额',
//                                        'format' => 'raw',
//                                        'value' => function ($model) {
//                                            return $model->amount;
//                                        },
//                                    ],

                                    [
                                        'headerOptions'=>['width'=>8],
                                        'header'=>'售后客服',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            if(isset($model->sale)){
                                                return $model->sale->real_name;
                                            }
                                            return '';
                                        },
                                    ],
                                    [
                                        'label'=>'写手交稿时间',
                                        'attribute' => 'publish_time',
                                        'value'=>function($model){
                                            if(isset($model->publish_time) && !empty($model->publish_time)){
                                                return $model->publish_time;
                                            }
                                            return  '';
                                        }
                                    ]

                                    ,'created_time','update_time',
                                    [
                                        'headerOptions'=>['width'=>80],
                                        'header'=>'状态类型',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return Yii::$app->params['orders_status'][$model->status];
                                        },
                                    ],
                                    [
                                        'headerOptions'=>['width'=>80],
                                        'header'=>'工作流状态',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return Yii::$app->params['orders_workflow'][$model->workflow];
                                        },
                                        'contentOptions' => function($model){
                                            if(($model->workflow == 2) || ($model->workflow == 6)){
                                                return ['bgcolor'=>'#FFFF00'];
                                            }
                                            return [];
                                        }
                                    ],
                                    [
                                        'label'=>'订单完成时间',
                                        'attribute' => 'finished',
                                        'value'=>function($model){
                                            if(isset($model->finished) && !empty($model->finished)){
                                                return $model->finished;
                                            }
                                            return  '';
                                        }
                                    ],
                                ];
                                //成本和利润售前和售后都看不到
                                if(\Yii::$app->user->can('p_accounter') || \Yii::$app->user->can('p_leader')){
//                                    $columns[]=[
//                                        'headerOptions'=>['width'=>1],
//                                        'header'=>'成本',
//                                        'format' => 'raw',
//                                        'value' => function ($model) {
//                                            return $model->cost_out;
//                                        },
//                                    ];

                                    $columns[] = [
                                        'headerOptions'=>['width'=>1],
                                        'header'=>'利润',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return $model->profit;
                                        },
                                    ];

                                }

                                $columns[]=[
                                    'class' => 'yii\grid\ActionColumn',
                                    'contentOptions' => [],
                                    'headerOptions'=>['width'=>75],
                                    'header'=>'操作',
                                    'template' => '{view} {edit}',
                                    'buttons' => [
                                        'view' => function ($url, $model) {
                                            return Html::a('查看', '/order/view?order_id='.$model->order_id, [
                                                'class' => 'btn btn-success btn-xs',
                                            ]);

                                        },
                                        'edit' => function ($url, $model) {
                                            if(1 === $model->status){
                                                return Html::a('编辑','/order/edit?order_id='.$model->order_id, [
                                                    'target'=>'_blank',
                                                    'class'=>'btn btn-primary btn-xs',
                                                ]);

                                            }
                                        },
                                        'cancel' => function ($url, $model) {
                                            if(\Yii::$app->user->can('p_front_sale') && 1 == $model->status && 1 == $model->workflow){
                                                return Html::a('取消', $url, [
                                                    'class'=>'btn btn-warning btn-xs',
                                                ]);
                                            }
                                        },

                                    ],
                                ];


                                echo GridView::widget([
                                    'layout' => "{items}{summary}{pager}",
                                    'tableOptions'=>['class' => 'table table-bordered table-striped dataTable'],
                                    'dataProvider' => $dataProvider,
                                    'rowOptions' => function($model) {
                                        if($model->profit < 0){
                                            if(\Yii::$app->user->can('p_accounter') || \Yii::$app->user->can('p_leader')) {
                                                return ['class' => 'danger'];
                                            }
                                        }
                                    },
                                    'columns' => $columns,
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


