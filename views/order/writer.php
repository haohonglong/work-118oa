<?php
/* @var $this \yii\web\View */
/* @var $content string */


use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\bootstrap\ActiveForm;

$this->title = '出帐账单列表';

?>
<script type="text/javascript">
    <?php $this->beginBlock('js'); ?>
    jQuery(function($){
        <?php
        if(isset($_GET['pay_time'])){
            echo "$('.daterange').val('{$_GET['pay_time']}');";
        }else{
            echo "$('.daterange').val('');";
        }
        ?>
        $(window).on('keydown',function(event){
            if(13 === event.keyCode){
                $('form [type=submit]').click();
            }
        });


    });


    <?php $this->endBlock(); ?>
</script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>
<style type="text/css">
    .table-account > tr{border-top: 1px solid #ddd;}
    .table-account tr td{
        border:none!important;
    }
</style>
<section class="content">
    <?php $form= ActiveForm::begin([
        'method'=>'GET',
        'options'=>['class'=>'form-inline'],
        'action'=>Url::toRoute(['/order/account-list-of-orders']),
    ])?>
    <div class="row MB20">
        <div class="col-sm-11">
            <div class="form-group">
                <label>订单号</label>
                <input type="text" class="form-control" placeholder="订单号" name="order_id" value="<?= isset($_GET['order_id'])? $_GET['order_id']:'';?>">
            </div>
            <div class="form-group">
                <label>收款人</label>
                <input type="text" class="form-control" placeholder="收款人" name="in_name" value="<?= isset($_GET['in_name'])? $_GET['in_name']:'';?>">
            </div>
            <div class="form-group">
                <label>题目</label>
                <input type="text" class="form-control" placeholder="题目" name="title" value="<?= isset($_GET['title'])? $_GET['title']:'';?>">
            </div>
            <div class="form-group">
                <label>转账时间</label>
                <input type="text"
                       class="form-control daterange"
                       name="pay_time"
                       value=""
                       readonly="readonly"
                >
            </div>

            <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> 查询</button>
        </div>
        <div class="col-sm-1"><a href="<?=Url::toRoute(['/order/account-list-of-orders-export',
                'order_id'=>isset($_GET['order_id'])? $_GET['order_id']:'',
                'pay_time'=>isset($_GET['pay_time'])? $_GET['pay_time']:'',
                'in_name'=>isset($_GET['in_name'])? $_GET['in_name']:'',
                'title'=>isset($_GET['title'])? $_GET['title']:'',
            ])?>">导出Excel</a></div>
    </div>
    <?php ActiveForm::end();?>
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
                                                <span class="MR10">订单号:<a target="_blank" href="<?=Url::toRoute(['/order/view', 'order_id' =>$items[0]['order_id']])?>"><?= $items[0]['order_id']?></a></span>
                                                <span>题目:<?= $items[0]['title']?></span>
                                            </p>
                                            <div class="box-tools">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body table-responsive no-padding">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <td width="6%">收款人名字</td>
                                                    <td width="5%">转账方式</td>
                                                    <td width="10%">转账时间</td>
                                                    <td width="5%">金额</td>
                                                    <td>备注</td>
                                                </tr>
                                                </thead>
                                                <tbody class="account-table">
                                                <?php foreach ($items as $v):?>
                                                    <tr data-id="<?=$v['id']?>">
                                                        <td width="7%"><?=$v['in_name']?></td>
                                                        <td width="6%"><?=$v['pay_type']?></td>
                                                        <td width="13%"><?=$v['pay_time']?></td>
                                                        <td width="6%"><?=$v['amount']?></td>
                                                        <td><?=$v['note']?></td>
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





