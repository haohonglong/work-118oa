<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\grid\GridView;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\bootstrap\ActiveForm;
use \yii\helpers\ArrayHelper;

$this->title = '报表列表';

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
        var $tr=null;
        var flag = false;
        var old_node=null;
        $('#dataTable tbody').on('click','tr[data-id]',function(event){
            if(flag && this === old_node){return;}//如果数据已经显示出来,又重复点击同一个位置就忽略
            old_node = this;
            if($tr){$tr.remove();flag = false;}
            var self = this;
            var uid = $(this).data('id');
            $.get("/report-form/index",{'uid':uid,'finished':'<?= isset($_GET['finished'])? $_GET['finished']:''?>','orders_status':'<?= isset($_GET['orders_status']) && $_GET['orders_status'] == '2'? $_GET['orders_status']:'0'?>','created':'<?= isset($_GET['created'])? $_GET['created']:''?>'},function(data){
                var html = template('dataTable:tp',{'data':data.models});
                $tr = $(html);
                $(self).after($tr);
                flag = true;
            },'json');
            event.stopPropagation();
        });

        $('#dataTable').on('click',function(event){
            event.stopPropagation();
        });

        $('.content').on('click',function () {
            if($tr){$tr.remove();flag = false;}
        });
        (function(){
            var $finished = $('[date-type="finished"]');
            var $label = $finished.find('label');
            $('#orderType').change(function(){
                if(2 == $(this).val()){
                    $label.text('完成日期');
                }else{
                    $label.text('创建日期');
                }
            });
        })();


    });


    <?php $this->endBlock(); ?>
</script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>

<!-- 转出模板-->
<script id="dataTable:tp" type="text/html">
    <tr>
        <td colspan="7">
            <table id="example2" class="table table-bordered table-hover dataTable"
                   role="grid" aria-describedby="example2_info">
                <thead>
                <tr role="row">
                    <th>订单号</th>
                    <th>题目</th>
                    <th>业务类型</th>
                    <th>字数</th>
                    <th>入账金额</th>
                    <th>出账金额</th>
                    <th>销售金额(订单金额)</th>
                    <th>利润</th>
                    <th>百分比</th>
                </tr>
                </thead>
                <tbody>

                <% for(var i=0;i < data.length;i++){
                var per = data[i]['per'];
                if(per<=45){
                color ='danger';
                }else if(per<=50){
                color ='warning';
                }else{
                color ='';
                }
                %>
                <tr role="row" class="<%= color%>">
                    <td><%= data[i]['order_id']%></td>
                    <td><%= data[i]['title']%></td>
                    <td><%= data[i]['type']%></td>
                    <td><%= data[i]['total_len']%></td>
                    <td><%= data[i]['cost_in']%></td>
                    <td><%= data[i]['cost_out']%></td>
                    <td><%= data[i]['amount']%></td>
                    <td><%= data[i]['profit']%></td>
                    <td><%= data[i]['per']%>%</td>
                </tr>
                <% }%>


                </tbody>

            </table>
        </td>
    </tr>

</script>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <?php $form= ActiveForm::begin([
                        'method'=>'GET',
                        'options'=>['class'=>'form-inline'],
                        'action'=>Url::toRoute(['/report-form/all']),
                    ])?>

                    <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group">
                                <label>订单状态</label>
                                <select name="orders_status" id="orderType">
                                    <option value="2" <?= !isset($_GET['orders_status']) || (isset($_GET['orders_status']) && 2 == $_GET['orders_status']) ? 'selected="selected"' : '' ?>>已完成</option>
                                    <option value="0" <?= isset($_GET['orders_status']) && 0 == $_GET['orders_status'] ? 'selected="selected"' : '' ?>>全部状态</option>
                                </select>
                            </div>
                            <div class="form-group ML20" date-type="finished">
                                <label><?= !isset($_GET['orders_status']) || (isset($_GET['orders_status']) && 2 == $_GET['orders_status']) ? '完成日期' : '创建日期' ?></label>
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
                        <div class="col-sm-1"><a href="<?=Url::toRoute(['report-form/export','finished'=>isset($_GET['finished'])? $_GET['finished']:'','orders_status'=>isset($_GET['orders_status']) && $_GET['orders_status'] == '2'? $_GET['orders_status']:'0'])?>">导出Excel</a></div>
                        <div class="col-sm-2"><a href="<?=Url::toRoute(['report-form/export-all-order','finished'=>isset($_GET['finished'])? $_GET['finished']:'','orders_status'=>isset($_GET['orders_status']) && $_GET['orders_status'] == '2'? $_GET['orders_status']:'0'])?>">导出Excel明细表</a></div>
                    </div>
                    <?php ActiveForm::end();?>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php

                            if (isset($dataProvider)){

                                echo GridView::widget([
                                    'layout' => "{items}{summary}{pager}",
                                    'tableOptions'=>['class' => 'table table-bordered table-hover','id'=>'dataTable'],
                                    'footerRowOptions'=>['class'=>'info'],
                                    'dataProvider' => $dataProvider,
                                    'showFooter'=>TRUE,
                                    'rowOptions' => function($model) {
                                        $uid = ['data-id'=>$model['id']];
                                        if($model['cost_in'] != 0){
                                            $per = round($model['profit']/$model['cost_in']*100);
                                            if($per<=45){
                                                $arr= ['class' => 'danger'];
                                            }elseif($per<=50){
                                                $arr= ['class' => 'success'];
                                            }
                                        }else{
                                            $arr = ['class' => 'danger'];
                                        }
                                        if($model['profit'] < 0){
                                            $arr = ['class' => 'danger'];
                                        }
                                        if(isset($arr)){
                                            return ArrayHelper::merge($uid,$arr);
                                        }else{
                                            return $uid;
                                        }


                                    },
                                    'columns' => [
                                        [
                                            'headerOptions'=>['width'=>10],
                                            'header'=>'售前',
                                            'format' => 'raw',
                                            'value' => function ($model, $key, $index, $column) {
                                                $column->footer = '总计:';
                                                return $model['real_name'];
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>20],
                                            'header'=>'总字数',
                                            'format' => 'raw',
                                            'value' => function ($model, $key, $index, $column) use($sum_arr) {
                                                $column->footer = $sum_arr['type_total'];
                                                return $model['type_total'];
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>30],
                                            'header'=>'入账金额',
                                            'format' => 'raw',
                                            'value' => function ($model, $key, $index, $column) use($sum_arr) {
                                                $column->footer = $sum_arr['cost_in'];
                                                return $model['cost_in'];
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>30],
                                            'header'=>'出账金额',
                                            'format' => 'raw',
                                            'value' => function ($model, $key, $index, $column) use($sum_arr) {
                                                $column->footer = $sum_arr['cost_out'];
                                                return $model['cost_out'];
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>30],
                                            'header'=>'销售总额(订单金额)',
                                            'format' => 'raw',
                                            'value' => function ($model, $key, $index, $column) use($sum_arr) {
                                                $column->footer = $sum_arr['orderAmount'];
                                                return $model['orderAmount'];
                                            },
                                        ],

                                        [
                                            'headerOptions'=>['width'=>30],
                                            'header'=>'总利润',
                                            'format' => 'raw',
                                            'value' => function ($model, $key, $index, $column) use($sum_arr) {
                                                $column->footer = $sum_arr['profit'];
                                                return $model['profit'];
                                            },
                                        ],
                                        [
                                            'headerOptions'=>['width'=>30],
                                            'header'=>'百分比',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                if($model['cost_in'] != 0){
                                                    $per = round($model['profit']/$model['cost_in']*100);
                                                    if($per<=45){
                                                        $str ='<span class="danger">'.$per.'%</span>';
                                                    }elseif($per<=50){
                                                        $str ='<span class="success">'.$per.'%</span>';
                                                    }else{
                                                        $str ='<span class="danger">'.$per.'%</span>';
                                                    }
                                                }else{
                                                    $str ='<span class="danger">0</span>';
                                                }
                                                return $str;
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

