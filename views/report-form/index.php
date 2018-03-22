<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\grid\GridView;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\bootstrap\ActiveForm;
use \yii\helpers\ArrayHelper;

$this->title = '所有订单';



?>
<script type="text/javascript">
    <?php $this->beginBlock('js'); ?>

    jQuery(function($){

        <?php
        if(isset($_GET['time'])){
            echo "$('.daterange').val('{$_GET['time']}');";
        }else{
            echo "$('.daterange').val('');";
        }
        ?>

        //点击全选checkbox 时,执行全选功能
        var $list = $('#types [type="checkbox"]');
        $(document).on('click','[data-select="all"]',function(){
            Service.checkboxSelectAll($list,this);
        });

        var $firstList = $('#select_first [type="checkbox"]');
        $(document).on('click','[data-select="first"]',function(){
            Service.checkboxSelectAll($firstList,this);
        });

        var $secondList = $('#select_second [type="checkbox"]');
        $(document).on('click','[data-select="second"]',function(){
            Service.checkboxSelectAll($secondList,this);
        });

        var $threeList = $('#select_three [type="checkbox"]');
        $(document).on('click','[data-select="three"]',function(){
            Service.checkboxSelectAll($threeList,this);
        });

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
                        'action'=>Url::toRoute(['/report-form/index']),
                    ])?>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group ML20" date-type="finished">
                                <select name="date_status" id="orderType">
                                    <option value="1" <?= !isset($_GET['date_status']) || (isset($_GET['date_status']) && 1 == $_GET['date_status']) ? 'selected="selected"' : '' ?>>完成日期</option>
                                    <option value="2" <?= isset($_GET['date_status']) && 2 == $_GET['date_status'] ? 'selected="selected"' : '' ?>>销售额统计(进行中和已完成)</option>
                                </select>
                                <input type="text"
                                       class="form-control daterange"
                                       name="time"
                                       value=""
                                       readonly="readonly"
                                >

                            </div>
                        </div>
                        <div class="col-sm-6" id="types">
                            <span>全选</span><input class="ML5" type="checkbox" data-select="all"  /><br />
                            <div style="margin-left: 15px">
                                            <div id="select_first">
                                <span>选择</span><input class="ML5" type="checkbox" data-select="first"  />
                                <?php $i=1;?>
                                <?php foreach (Yii::$app->params['orders_type'] as $item){?>
                                    <?php
                                        if($i<=6){?>
                                                <label>
                                                    <input type="checkbox" name="types[]"
                                                        <?php if(isset($types) && in_array($item,$types)){?>
                                                            checked="checked"
                                                        <?php }?>
                                                           value="<?=$item?>"><span class="ML5"><?=$item?></span>
                                                </label>
                                        <?php }
                                    ?>

                                    <?php

                                    $i++;
                                }?>
                                            </div>
                            </div>


                            <div style="margin-left: 15px">
                                <div id="select_second">
                                    <span>选择</span><input class="ML5" type="checkbox" data-select="second"  />
                                    <?php $i=1;?>
                                    <?php foreach (Yii::$app->params['orders_type'] as $item){?>
                                        <?php
                                        if($i>6 && $i<=12){?>
                                            <label>
                                                <input type="checkbox" name="types[]"
                                                    <?php if(isset($types) && in_array($item,$types)){?>
                                                        checked="checked"
                                                    <?php }?>
                                                       value="<?=$item?>"><span class="ML5"><?=$item?></span>
                                            </label>
                                        <?php }
                                        ?>

                                        <?php

                                        $i++;
                                    }?>
                                </div>
                            </div>


                            <div style="margin-left: 15px">
                                <div id="select_three">
                                    <span>选择</span><input class="ML5" type="checkbox" data-select="three"  />
                                    <?php $i=1;?>
                                    <?php foreach (Yii::$app->params['orders_type'] as $item){?>
                                        <?php
                                        if($i>12){?>
                                            <label>
                                                <input type="checkbox" name="types[]"
                                                    <?php if(isset($types) && in_array($item,$types)){?>
                                                        checked="checked"
                                                    <?php }?>
                                                       value="<?=$item?>"><span class="ML5"><?=$item?></span>
                                            </label>
                                        <?php }
                                        ?>

                                        <?php

                                        $i++;
                                    }?>
                                </div>
                            </div>
                        </div>

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
                            <?=$this->render('_dataTable.php',['data'=>$data,'sum_arr'=>$sum_arr])?>
                        </div>
                    </div>
                    <?php if(isset($data) && !empty($data)){?>
                        <div class="row">
                            <div class="col-sm-9"></div>
                            <div class="col-sm-1">
                                <?php $form= ActiveForm::begin([
                                    'method'=>'POST',
                                    'options'=>['class'=>'form-inline'],
                                    'action'=>Url::toRoute(['/report-form/export-of-frontsale']),
                                ])?>

                                <input type="hidden" name="orders" value="<?=base64_encode(json_encode($ids));?>">
                                <input type="hidden" name="url" value="<?=Url::toRoute(['/report-form/index'])?>">
                                <input type="hidden" name="title" value="售前">
                                <button type="submit" class="btn btn-default">导出Excel</button>
                                <?php ActiveForm::end();?>

                            </div>
                            <div class="col-sm-2">
                                <?php $form= ActiveForm::begin([
                                    'method'=>'POST',
                                    'options'=>['class'=>'form-inline'],
                                    'action'=>Url::toRoute(['/report-form/export-of-orders']),
                                ])?>
                                <input type="hidden" name="orders" value="<?=base64_encode(json_encode($ids));?>">
                                <input type="hidden" name="url" value="<?=Url::toRoute(['/report-form/index'])?>">
                                <input type="hidden" name="title" value="售前所有订单">
                                <button type="submit" class="btn btn-default">导出Excel明细表</button>
                                <?php ActiveForm::end();?>
                            </div>
                        </div>
                    <?php }?>

                </div>
            </div>
        </div>
    </div>
</section>

