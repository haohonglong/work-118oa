<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\grid\GridView;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\bootstrap\ActiveForm;
use \yii\helpers\ArrayHelper;

$this->title = '提成结算';



?>
<script type="text/javascript">
    <?php $this->beginBlock('js'); ?>
    jQuery(function($){

        $('#settlement-form').on('beforeSubmit', function (e) {
            if (!confirm("确定要结算吗?")) {
                return false;
            }
            return true;
        });

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
                        'action'=>Url::toRoute(['/report-form/settlement']),
                    ])?>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>结算状态</label>
                                <select name="check_status" id="orderType">
                                    <option value="0" <?= !isset($_GET['check_status']) || (isset($_GET['check_status']) && 0 == $_GET['check_status']) ? 'selected="selected"' : '' ?>>未结算</option>
                                    <option value="1" <?= isset($_GET['check_status']) && 1 == $_GET['check_status'] ? 'selected="selected"' : '' ?>>已结算</option>
                                </select>
                            </div>
                            <div class="form-group ML10">
                                <label>创建日期:</label>
                                <select name="year" id="">
                                    <option value="null">请选择年</option>
                                    <?php
                                    $current = date('Y');
                                    $year = "2017";
                                    for(;$year<=$current;$year++){?>
                                        <option value="<?=$year?>" <?php if(isset($_GET['year']) && $year == $_GET['year']){echo 'selected="selected"';} ?>><?=$year?></option>
                                    <?php }?>
                                </select>
                                年
                                <select name="month" id="">
                                    <option value="null">请选择月</option>
                                    <option value="01" <?php if(isset($_GET['month']) && '01' == $_GET['month']){echo 'selected="selected"';} ?>>一</option>
                                    <option value="02" <?php if(isset($_GET['month']) && '02' == $_GET['month']){echo 'selected="selected"';} ?>>二</option>
                                    <option value="03" <?php if(isset($_GET['month']) && '03' == $_GET['month']){echo 'selected="selected"';} ?>>三</option>
                                    <option value="04" <?php if(isset($_GET['month']) && '04' == $_GET['month']){echo 'selected="selected"';} ?>>四</option>
                                    <option value="05" <?php if(isset($_GET['month']) && '05' == $_GET['month']){echo 'selected="selected"';} ?>>五</option>
                                    <option value="06" <?php if(isset($_GET['month']) && '06' == $_GET['month']){echo 'selected="selected"';} ?>>六</option>
                                    <option value="07" <?php if(isset($_GET['month']) && '07' == $_GET['month']){echo 'selected="selected"';} ?>>七</option>
                                    <option value="08" <?php if(isset($_GET['month']) && '08' == $_GET['month']){echo 'selected="selected"';} ?>>八</option>
                                    <option value="09" <?php if(isset($_GET['month']) && '09' == $_GET['month']){echo 'selected="selected"';} ?>>九</option>
                                    <option value="10" <?php if(isset($_GET['month']) && '10' == $_GET['month']){echo 'selected="selected"';} ?>>十</option>
                                    <option value="11" <?php if(isset($_GET['month']) && '11' == $_GET['month']){echo 'selected="selected"';} ?>>十一</option>
                                    <option value="12" <?php if(isset($_GET['month']) && '12' == $_GET['month']){echo 'selected="selected"';} ?>>十二</option>
                                </select>
                                月

                            </div>
                        </div>
                        <div class="col-sm-7" id="types">
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
                            <div class="col-sm-8"></div>
                            <div class="col-sm-1">
                                <?php $form= ActiveForm::begin([
                                'method'=>'POST',
                                'options'=>['class'=>'form-inline'],
                                'action'=>Url::toRoute(['/report-form/export-of-frontsale']),
                                ])?>
                                <input type="hidden" name="orders" value="<?=base64_encode(json_encode($ids));?>">
                                <input type="hidden" name="url" value="<?=Url::toRoute(['/report-form/settlement'])?>">
                                <input type="hidden" name="title" value="提成结算">
                                <button type="submit" class="btn btn-default">导出Excel</button>
                                <?php ActiveForm::end();?>

                            </div>

                            <div class="col-sm-1">
                                <?php $form= ActiveForm::begin([
                                'method'=>'POST',
                                'options'=>['class'=>'form-inline'],
                                'action'=>Url::toRoute(['/report-form/export-of-orders']),
                                ])?>
                                <input type="hidden" name="orders" value="<?=base64_encode(json_encode($ids));?>">
                                <input type="hidden" name="url" value="<?=Url::toRoute(['/report-form/settlement'])?>">
                                <input type="hidden" name="title" value="提成结算-所有订单">
                                <button type="submit" class="btn btn-default">导出Excel明细表</button>
                                <?php ActiveForm::end();?>
                            </div>
                            <?php if(isset($ids) && is_array($ids) && !empty($ids) && (!isset($_GET['check_status']) || (isset($_GET['check_status'])) && 0 == $_GET['check_status'])){ ?>
                            <div class="col-sm-1"></div>
                            <div class="col-sm-1">
                            <?php $form= ActiveForm::begin([
                                'id' => 'settlement-form',
                                'method'=>'POST',
                                'options'=>['class'=>'form-inline'],
                                'action'=>Url::toRoute(['/report-form/checkout']),
                            ])?>
                            <?php foreach ($ids as $item){?>
                            <input type="hidden" name="ids[]" value="<?=$item?>">
                            <?php }?>
                            <button type="submit" class="btn btn-primary"> 结算</button>

                            <?php ActiveForm::end();?>
                        </div>
                        <?php }?>
                    </div>
                    <?php }?>

                </div>
            </div>
        </div>
    </div>
</section>

