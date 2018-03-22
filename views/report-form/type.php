<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\grid\GridView;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\bootstrap\ActiveForm;
use \yii\helpers\ArrayHelper;

$this->title = '按类型统计';

?>
<script type="text/javascript">
    <?php $this->beginBlock('js'); ?>
    jQuery(function($){
        <?php
        if(isset($_GET['finished'])){
            echo "$('.daterange').val('{$_GET['finished']}');";
        }else{
            echo "$('.daterange').val('');";
        }
        ?>

    });


    <?php $this->endBlock(); ?>
</script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <?php $form= ActiveForm::begin([
                        'method'=>'GET',
                        'options'=>['class'=>'form-inline'],
                        'action'=>Url::toRoute(['/report-form/type']),
                    ])?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>入账日期</label>
                                <input type="text"
                                       class="form-control daterange"
                                       name="finished"
                                       value=""
                                       readonly="readonly"
                                >
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> 查询</button>
                            </div>

                        </div>
                    </div>
                    <?php ActiveForm::end();?>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">已完成</h3>
                    <div class="box-tools pull-right">
                        <a href="<?=Url::toRoute(['report-form/export-type','finished'=>isset($_GET['finished'])? $_GET['finished']:''])?>">导出Excel已完成</a>
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="example2" class="table table-bordered table-hover dataTable"
                                       role="grid" aria-describedby="example2_info">
                                    <thead>
                                    <tr role="row">
                                        <th>业务类型</th>
                                        <th>单量</th>
                                        <th>订单金额</th>
                                        <th>字数</th>
                                        <th>入账金额</th>
                                        <th>出账金额</th>
                                        <th>利润</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($list as $item):?>
                                        <tr role="row">
                                            <td><?=$item['type']?></td>
                                            <td><?=$item['num']?></td>
                                            <td><?=$item['amount']?></td>
                                            <td><?=$item['total_len']?></td>
                                            <td><?=$item['cost_in']?></td>
                                            <td><?=$item['cost_out']?></td>
                                            <td><?=$item['profit']?></td>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                    <tfoot>
                                    <tr role="row">
                                        <th>总计:</th>
                                        <th><?=$total['num']?></th>
                                        <th><?=$total['amount']?></th>
                                        <th><?=$total['total_len']?></th>
                                        <th><?=$total['cost_in']?></th>
                                        <th><?=$total['cost_out']?></th>
                                        <th><?=$total['profit']?></th>
                                    </tr>
                                    </tfoot>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">未完成</h3>
                    <div class="box-tools pull-right">
                        <a class="ML10" href="<?=Url::toRoute(['report-form/export-type2','finished'=>isset($_GET['finished'])? $_GET['finished']:''])?>">导出Excel未完成</a>
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="example2" class="table table-bordered table-hover dataTable"
                                       role="grid" aria-describedby="example2_info">
                                    <thead>
                                    <tr role="row">
                                        <th>业务类型</th>
                                        <th>单量</th>
                                        <th>订单金额</th>
                                        <th>字数</th>
                                        <th>入账金额</th>
                                        <th>出账金额</th>
                                        <th>利润</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($unfinishedList as $item):?>
                                        <tr role="row">
                                            <td><?=$item['type']?></td>
                                            <td><?=$item['num']?></td>
                                            <td><?=$item['amount']?></td>
                                            <td><?=$item['total_len']?></td>
                                            <td><?=$item['cost_in']?></td>
                                            <td><?=$item['cost_out']?></td>
                                            <td><?=$item['profit']?></td>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                    <tfoot>
                                    <tr role="row">
                                        <th>总计:</th>
                                        <th><?=$un_total['num']?></th>
                                        <th><?=$un_total['amount']?></th>
                                        <th><?=$un_total['total_len']?></th>
                                        <th><?=$un_total['cost_in']?></th>
                                        <th><?=$un_total['cost_out']?></th>
                                        <th><?=$un_total['profit']?></th>
                                    </tr>
                                    </tfoot>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">黄稿统计</h3>
                    <div class="box-tools">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="example2" class="table table-bordered table-hover dataTable"
                                       role="grid" aria-describedby="example2_info">
                                    <thead>
                                    <tr role="row">
                                        <th>单量</th>
                                        <th>订单金额</th>
                                        <th>利润</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr role="row">
                                        <td><?=$yellow_all['num']?></td>
                                        <td><?=$yellow_all['amount']?></td>
                                        <td><?=$yellow_all['profit']?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

