<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\widgets\ActiveForm;
use \yii\helpers\Html;
use \yii\helpers\Url;
use app\models\User;

$this->title = '订单';
//约定时间 可选工作日
$appointed_time_limit_date = [1,2,3,4,5,10,15,20,30];


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

        <?php $this->endBlock(); ?>
    </script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            <h4>订单编号:<?= $model->order_id?> <small class="font-blue" style="margin-left: 10px;">状态:<?= $workflow?></small></h4>


                        </div>
                    </div>
                    <div class="box-body">
                        <?php $form= ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']])?>
                        <div class="row">
                            <div class="col-sm-2">
                                <?= $form->field($model, 'guest_name') ?>
                                <?= $form->field($model, 'title') ?>
                                <?php if(\Yii::$app->user->can('p_accounter') || \Yii::$app->user->can('p_leader')){?>
                                <div class="form-group">
                                    <label class="control-label">总利润:</label>
                                    <span id="profit"><?= $model->profit?></span>
                                </div>
                                <?php }?>
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
                                <?= $form->field($model, 'publish_time')->textInput([
                                    'class'=>'form-control datepicker',
                                    'data-provide'=>'datepicker',
                                    'data-date-format'=>'yyyy-mm-dd',
                                    'readonly'=>'readonly',
                                ]) ?>

                            </div>
                            <div class="col-sm-2">

                                <?= $form->field($model, 'appointed_time')->textInput([
                                    'class'=>'form-control datepicker',
                                    'data-provide'=>'datepicker',
                                    'data-date-format'=>'yyyy-mm-dd',
                                    'readonly'=>'readonly',
                                ]) ?>
                                <?= $form->field($model, 'after_sale')->hiddenInput(
                                    ['readonly'=>'readonly']
                                ); ?>
                                <span id="show-after_sale" style="position: relative;top:-10px;">
                                    <?php echo isset($model->after_sale) ? User::getRealName($model->after_sale) : '' ?>
                                </span>

                            </div>
                            <div class="col-sm-2">
                                <select class="form-control MT25" id="appointed_time_limit_date">
                                    <option value="0" selected="selected">选择工作日</option>
                                    <?php foreach ($appointed_time_limit_date as $i):?>
                                    <option value="<?= date('Y-m-d H:i',strtotime('+'.$i.' days'));?>"><?= $i?>个工作日</option>
                                    <?php endforeach;?>
                                </select>
                                <?php if(!(isset($model->after_sale) && !empty($model->after_sale)) || \Yii::$app->user->can('p_leader')):?>
                                <select class="form-control" style="margin-top: 40px;" id="after_sales">
                                    <option value="0">请指派一个售后</option>
                                    <?php foreach ($after_sales as $item):?>
                                    <option value="<?=$item['username']?>"><?=$item['real_name']?></option>
                                    <?php endforeach;?>
                                </select>
                                <?php endif;?>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">上传文件</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadFileModal">上传文件</button>
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive no-padding">
                                        <table class="table table-hover table-account">
                                            <thead>
                                            <tr>
                                                <th width="70%">文件标题</th>
                                                <th width="">是否有效</th>
                                                <th width="">文件类型</th>
                                                <th>创建时间</th>
                                                <th width="9%">操作</th>
                                            </tr>
                                            </thead>
                                            <tbody id="uploadFile">
                                                <template v-for="item in items">
                                                    <tr :data-id="item.id">
                                                        <td>{{item.title}}</td>
                                                        <td class="valid">{{item.valid}}</td>
                                                        <td>{{item.fileType}}</td>
                                                        <td>{{item.created_time}}</td>
                                                        <td>
                                                            <a class="downloadfile btn btn-primary btn-sm btn-success btn-xs btn-info" :href="'/upload-file/download?id='+item.id">下载</a>
                                                            <button type="button" class="btn btn-primary btn-sm btn-success btn-xs btn-valid">有效</button>
                                                            <button type="button" class="btn btn-info btn-sm btn-danger btn-xs btn-invalid">无效</button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">入账</h3>
                                        <div class="box-tools">
                                            <?php if(!(\Yii::$app->user->can('p_accounter'))){?>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#accountModalIn">添加入账</button>
                                            <?php }?>
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive no-padding">
                                        <table class="table table-hover table-account">
                                            <thead>
                                            <tr>
                                                <th width="80">创建人</th>
                                                <th width="200">流水号</th>
                                                <th width="150">转账时间</th>
                                                <th width="80">转账方式</th>
                                                <th width="80">金额</th>
                                                <th>帐单备注</th>
                                                <th width="100">状态</th>
                                                <th width="180">操作</th>
                                            </tr>
                                            </thead>
                                            <tbody id="account_in"></tbody>
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>

                        <?php if(\Yii::$app->user->can('p_after_sale') || \Yii::$app->user->can('p_accounter')): //售前看不到出账?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">出账</h3>
                                        <div class="box-tools">
                                            <?php if(!(\Yii::$app->user->can('p_accounter'))){?>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#accountModalOut">申请出账</button>
                                            <?php }?>
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive no-padding">
                                        <table class="table table-hover table-account">
                                            <thead>
                                            <tr>
                                                <th width="80">创建人</th>
                                                <th width="200">流水号</th>
                                                <th width="150">转账时间</th>
                                                <th width="80">转账方式</th>
                                                <th width="80">金额</th>
                                                <th>帐单备注</th>
                                                <th width="100">状态</th>
                                                <th width="180">操作</th>
                                            </tr>
                                            </thead>
                                            <tbody id="account_out"></tbody>
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                        <?php endif;?>

                        <div class="row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'note')->textarea(['rows'=>'12']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <?= $form->field($model, 'workflow')
                                    ->label('修改工作流状态:')
                                    ->dropDownList(Yii::$app->params['orders_workflow'],['class'=>'form-inline']); ?>
                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($model, 'status')
                                    ->label('修改状态类型:')
                                    ->dropDownList(Yii::$app->params['orders_status'],['class'=>'form-inline']); ?>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <?= Html::submitButton('修改', ['class' => 'btn btn-primary']) ?>

                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end()?>
                        <!--日志-->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">日志</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body table-responsive no-padding">
                                        <ul class="timeline" id="log_timeline">
                                            <template v-for="item in items">
                                                <li>
                                                    <div class="timeline-item">
                                                        <span class=""><i class="fa fa-clock-o"></i>{{item.create_time}}</span> 用户: {{item.username}}
                                                        <div class="timeline-body">
                                                            {{item.name}} {{item.content}}
                                                        </div>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!--入账模态框-->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="accountModalIn">
        <div class="modal-dialog" role="document">
            <div class="modal-content content">
                <?php $form= ActiveForm::begin([
                    'id'=>'accountscreateformIn',
                    'action'=>Url::toRoute(['/account/save-in-account']),
                ])?>
                <input type="hidden" name="AccountsInCreateForm[order_id]" value="<?= $model->order_id?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">添加入账</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($accountInModel, 'pay_type') ?>
                            <?= $form->field($accountInModel, 'pay_time')->textInput([
                                'class'=>'form-control datepicker',
                                'data-provide'=>'datepicker',
                                'data-date-format'=>'yyyy-mm-dd',
                                'readonly'=>'readonly',
                            ]) ?>
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                            <?= $form->field($accountInModel, 'serial_number') ?>
                            <?= $form->field($accountInModel, 'amount') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($accountInModel, 'note')->textarea(['rows'=>'12']) ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
                <?php ActiveForm::end();?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!--修改入账模态框-->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="accountInModalEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content content">
                <?php $form= ActiveForm::begin([
                    'id'=>'accountsInCreateFormEdit',
                    'action'=>Url::toRoute(['/account/account-in-edit']),
                ])?>
                <input type="hidden" id="accountsineditform-id" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">修改入账</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($accountInEditModel, 'pay_type') ?>
                            <?= $form->field($accountInEditModel, 'pay_time')->textInput([
                                'class'=>'form-control datepicker',
                                'data-provide'=>'datepicker',
                                'data-date-format'=>'yyyy-mm-dd',
                                'readonly'=>'readonly',
                            ]) ?>
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                            <?= $form->field($accountInEditModel, 'serial_number') ?>
                            <?= $form->field($accountInEditModel, 'amount') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($accountInEditModel, 'note')->textarea(['rows'=>'12']) ?>
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
    </div><!-- /.modal -->



    <!--出账模态框-->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="accountModalOut">
        <div class="modal-dialog" role="document">
            <div class="modal-content content">
                <?php $form= ActiveForm::begin([
                    'id'=>'accountscreateformOut',
                    'action'=>Url::toRoute(['/account/save-out-account']),
                ])?>
                <input type="hidden" name="AccountsOutCreateForm[order_id]" value="<?= $model->order_id?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">申请出账</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($accountOutModel, 'pay_type') ?>
                            <?= $form->field($accountOutModel, 'pay_time')->textInput([
                                'class'=>'form-control datepicker',
                                'data-provide'=>'datepicker',
                                'data-date-format'=>'yyyy-mm-dd',
                                'readonly'=>'readonly',
                                'value'=>date("Y-m-d H:i"),
                            ]) ?>
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                            <?= $form->field($accountOutModel, 'serial_number') ?>
                            <?= $form->field($accountOutModel, 'amount') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($accountOutModel, 'in_name') ?>
                            <?= $form->field($accountOutModel, 'in_account_number') ?>

                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                            <?= $form->field($accountOutModel, 'in_openaccount') ?>
                            <?= $form->field($accountOutModel, 'in_zipcode') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($accountOutModel, 'in_address') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($accountOutModel, 'note')->textarea(['rows'=>'12']) ?>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
                <?php ActiveForm::end();?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!--修改出账模态框-->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="accountOutModalEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content content">
                <?php $form= ActiveForm::begin([
                    'id'=>'accountsOutCreateFormEdit',
                    'action'=>Url::toRoute(['/account/account-out-edit']),
                ])?>
                <input type="hidden" id="accountsouteditform-id" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">修改出账</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($accountOutEditModel, 'pay_time')->textInput([
                                'class'=>'form-control datepicker',
                                'data-provide'=>'datepicker',
                                'data-date-format'=>'yyyy-mm-dd',
                                'readonly'=>'readonly',
                            ]) ?>
                            <?= $form->field($accountOutEditModel, 'pay_type') ?>
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                            <?= $form->field($accountOutEditModel, 'serial_number') ?>
                            <?= $form->field($accountOutEditModel, 'amount') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($accountOutEditModel, 'in_name') ?>
                            <?= $form->field($accountOutEditModel, 'in_account_number') ?>

                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                            <?= $form->field($accountOutEditModel, 'in_openaccount') ?>
                            <?= $form->field($accountOutEditModel, 'in_zipcode') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($accountOutEditModel, 'in_address') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($accountOutEditModel, 'note')->textarea(['rows'=>'12']) ?>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <?php if(\Yii::$app->user->can('p_accounter')):?>
                    <button type="button" class="btn btn-primary btn-confirm" data-dismiss="modal">确认转帐</button>
                    <button type="button" class="btn btn-info btn-deny" data-dismiss="modal">拒绝</button>
                    <?php endif;?>
                    <button type="submit" class="btn btn-primary">修改</button>
                </div>
                <?php ActiveForm::end();?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



    <!--文件上传模态框-->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="uploadFileModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content content">
                <?php $form= ActiveForm::begin([
                    'id'=>'uploadform',
                    'options' => ['enctype' => 'multipart/form-data'],
                    'action'=>Url::toRoute(['/upload-file/upload']),
                ])?>

                <input type="hidden" name="UploadForm[order_id]" value="<?= $model->order_id?>">
                <input type="hidden" name="order_id" value="<?= $oder_id?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">上传文件</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($uploadFileModel, 'title') ?>
                            <?= $form->field($uploadFileModel, 'imageFile')->fileInput() ?>
                        </div>

                    </div>
                </div>
                <div class="progress" style="display: none;">
                    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                        <span class="sr-only" style="position: static;">40% Complete (success)</span>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">上传</button>
                </div>
                <?php ActiveForm::end();?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->




    <!-- 转入模板-->
    <script id="account-in:tp" type="text/html">
        <% for(var i=0;i< data.length;i++){%>
        <tr class="" data-id="<%= data[i]['id']%>">
            <td><%= data[i]['real_name']%></td>
            <td><%= data[i]['serial_number']%></td>
            <td><%= data[i]['pay_time']%></td>
            <td><%= data[i]['pay_type']%></td>
            <td><%= data[i]['amount']%></td>
            <td><%= data[i]['note']%></td>
            <td>
                <% if(1 == data[i]['check_status']){%>
                    <span class="label label-warning">未审核</span>
                <% }else if(2 == data[i]['check_status']){%>
                    <span class="label label-danger">已拒绝</span>
                <% }else if(3 == data[i]['check_status']){%>
                    <span class="label label-success">已审核</span>
                <% }%>

            </td>
            <td>
                <% if(1 == data[i]['check_status']){%>
                <?php if(\Yii::$app->user->can('p_accounter')):?>
                    <button type="button" class="btn btn-primary btn-sm btn-confirm">确认</button>
                    <button type="button" class="btn btn-info btn-sm btn-deny">拒绝</button>
                <?php endif;?>
                <button type="button" class="btn btn-primary btn-sm btn-edit" data-toggle="modal" data-target="#accountInModalEdit">修改</button>
                <% }%>
            </td>
        </tr>
        <% }%>
    </script>

    <!-- 转出模板-->
    <script id="account-out:tp" type="text/html">
        <% for(var i=0;i< data.length;i++){%>
        <tr class="" data-id="<%= data[i]['id']%>">
            <td><%= data[i]['real_name']%></td>
            <td><%= data[i]['serial_number']%></td>
            <td><%= data[i]['pay_time']%></td>
            <td><%= data[i]['pay_type']%></td>
            <td><%= data[i]['amount']%></td>
            <td><%= data[i]['note']%></td>
            <td>
                <% if(1 == data[i]['check_status']){%>
                    <span class="label label-warning">未审核</span>
                <% }else if(2 == data[i]['check_status']){%>
                    <span class="label label-danger">已拒绝</span>
                <% }else if(3 == data[i]['check_status']){%>
                    <span class="label label-success">已审核</span>
                <% }%>

            </td>
            <td>
                <?php if(\Yii::$app->user->can('p_accounter') || \Yii::$app->user->can('p_after_sale')):?>
                    <% if(1 == data[i]['check_status']){%>
                        <button type="button" class="btn btn-primary btn-sm btn-edit" data-toggle="modal" data-target="#accountOutModalEdit">修改</button>
                    <% }%>
                <?php endif;?>
            </td>
        </tr>
        <% }%>
    </script>




    <script type="text/javascript">
        <?php $this->beginBlock('js'); ?>
        Service.setMenuSelectedStatus('/order/index');

        Service.getLogs=function (){
            $.post("/log/show-logs",{'order_id':'<?= $model->order_id?>'},function(data){
                if (data.status == 1){
                    new Vue({
                        el: '#log_timeline',
                        data: {
                            items: data.data
                        }
                    });
                }
            },'json');
        };
        Service.getListData=function(name,account_type){
            $.post("/account/show-account-list",{'account_type':account_type,'order_id':'<?= $model->order_id?>'},function(data){
                if (data.status == 1){
                    if(1 === account_type){
                        $('#'+name).html(template('account-in:tp',{'data':data.data}));
                    }else if(2 === account_type){
                        $('#'+name).html(template('account-out:tp',{'data':data.data}));
                    }
                }
            },'json');
        };



        Service.getFilesList=function(){

            new Vue({
                el: '#uploadFile',
                data: {
                    items: <?= json_encode($filelist)?>
                },
                created:function(){
                    $.each(this.items,function(){
                        if(0 == this.valid){
                            this.valid = '无效';
                        }else{
                            this.valid = '有效';
                        }
                        this.fileType = Service.parseFileType(this.type);
                    });
                }
            });

        };



        jQuery(function($){
            var account_in,account_out;
            //防止工作流往回退
            var defaultOptinVal= $("#ordereditform-workflow").find("option:selected").val();
            $("#ordereditform-workflow").change(function () {
                var $this = $(this);
                if(defaultOptinVal > $this.val() || (parseInt($this.val()) - parseInt(defaultOptinVal)) > 1){
                    $this.val(defaultOptinVal);
                }

            });

            Service.getFilesList();

            function main(){
                account_in = 'account_in';
                account_out = 'account_out';
                Service.getLogs();
                Service.getListData(account_in,1);
                Service.getListData(account_out,2);
            }

            main();

            function doAccount(url,id){
                if(confirm('确定要提交吗?')){
                    $.post(url,{'id':id,'order_id':'<?= $model->order_id?>'},function(data){
                        if (data.status == 1){
                            main();
                            window.location.reload();
                        }else{
                            Service.getLogs();
                        }

                    },'json');
                }
            }


            //财务确认这笔款到账,修改状态
            $('.table-account').on('click','.btn-confirm',function(){
                doAccount('/account/confirm-mony',$(this).closest('tr').data().id);
            });

            //财务拒绝这笔款
            $('.table-account').on('click','.btn-deny',function(){
                doAccount('/account/deny-mony',$(this).closest('tr').data().id);
            });

            //财务确认这笔款到账,修改状态
            $('#accountOutModalEdit').on('click','.btn-confirm',function(){
                var id= $(this).closest('form').find('#accountsouteditform-id').val();
                doAccount('/account/confirm-mony',id);
            });

            //财务拒绝这笔款
            $('#accountOutModalEdit').on('click','.btn-deny',function(){
                var id= $(this).closest('form').find('#accountsouteditform-id').val();
                doAccount('/account/deny-mony',id);
            });

            //添加入账
            $('form#accountscreateformIn').on('beforeSubmit', function(e) {
                $.post($(this).attr('action'),$(this).serialize(),function(data){
                    if (1 === data.status){
                        Service.getListData(account_in,1);
                        var $modal=$('#accountModalIn');
                        $modal.modal('hide');
                        Service.from.clearInputVal($modal);
                        Service.getLogs();

                    }
                },'json');
                return false;
            }).on('submit',function(e){
                e.preventDefault();
            });
            //点击入账修改按钮是取出对应数据copy到修改入账的模态框里
            $('#account_in').on('click','.btn-edit',function(){
                var account_content=['id','pay_time','serial_number','amount','note','pay_type'];
                var $tr=$(this).closest('tr');
                $.post('/account/get-account-by-id',{'id':$tr.data().id},function(data){
                    if (data.status == 1){
                        $.each(data.data,function(k,v){
                            if($.inArray(k,account_content) !== -1){
                                $('#accountsineditform-'+k).val(v);
                            }
                        });

                    }else{

                    }

                },'json');
            });

            //修改入账账单
            $('form#accountsInCreateFormEdit').on('beforeSubmit', function(e) {
                var id=$('#accountsineditform-id').val();
                $.post($(this).attr('action'),$(this).serialize()+'&id='+id,function(data){
                    if (1 === data.status){
                        Service.getListData(account_in,1);
                        var $modal=$('#accountInModalEdit');
                        $modal.modal('hide');
                        Service.getLogs();
                    }
                },'json');
                return false;
            }).on('submit',function(e){
                e.preventDefault();
            });

            //申请出账
            $('form#accountscreateformOut').on('beforeSubmit', function(e) {
                $.post($(this).attr('action'),$(this).serialize(),function(data){
                    if (1 === data.status){
                        Service.getListData(account_out,2);
                        var $modal=$('#accountModalOut');
                        $modal.modal('hide');
                        Service.from.clearInputVal($modal);
                        Service.getLogs();

                    }
                },'json');
                return false;
            }).on('submit',function(e){
                e.preventDefault();
            });

            //点击出账修改按钮是取出对应数据copy到修改出账的模态框里
            $('#account_out').on('click','.btn-edit',function(){
                var account_content=['id','pay_time','serial_number','amount','in_account_number','in_address','in_name','in_openaccount','in_zipcode','note','pay_type'];
                var $tr=$(this).closest('tr');
                $.post('/account/get-account-by-id',{'id':$tr.data().id},function(data){
                    if (data.status == 1){
                        $.each(data.data,function(k,v){
                            if($.inArray(k,account_content) !== -1){
                                $('#accountsouteditform-'+k).val(v);
                            }
                        });

                    }else{

                    }

                },'json');
            });

            //修改出账账单
            $('form#accountsOutCreateFormEdit').on('beforeSubmit', function(e) {
                var id=$('#accountsouteditform-id').val();
                $.post($(this).attr('action'),$(this).serialize()+'&id='+id,function(data){
                    if (1 === data.status){
                        Service.getListData(account_out,2);
                        var $modal=$('#accountOutModalEdit');
                        $modal.modal('hide');
                        Service.getLogs();
                    }
                },'json');
                return false;
            }).on('submit',function(e){
                e.preventDefault();
            });
            

            //约定时间工作日选择
            $('#appointed_time_limit_date').on('change',function(){
                var date = $(this).val();
                $('#ordereditform-appointed_time').val(date);
            });

            //售后指定售后
            $('#after_sales').on('change',function(){
                var $option = $(this).find("option:selected");
                var name = $option.text();
                var n = $(this).val();
                if(0 == n){return true;}
                $('#ordereditform-after_sale').val($option.val());
                $('#show-after_sale').text(name);
            });
            //设置文件有效
            $('.btn-valid').on('click',function(){
                var $tr = $(this).closest('tr');
                var id = $tr.data().id;
                var $valid = $tr.find('.valid');
                if($valid.text() != '有效'){
                    G_dataBox.UploadFile.valid({
                        order_id:'<?= $model->order_id?>',
                        id:id,
                        valid:1
                    },function(data){
                        if(1 == data.status){
                            $valid.text('有效');
                            Service.getLogs();

                        }
                    });

                }

            });

            //设置文件无效
            $('.btn-invalid').on('click',function(){
                var $tr = $(this).closest('tr');
                var id = $tr.data().id;
                var $valid = $tr.find('.valid');
                if($valid.text() != '无效'){
                    G_dataBox.UploadFile.valid({
                        order_id:'<?= $model->order_id?>',
                        id:id,
                        valid:0
                    },function(data){
                        if(1 == data.status){
                            $valid.text('无效');
                            Service.getLogs();

                        }
                    });

                }

            });

            //上传文件进度条
            Service.UploadFile.run();
            $('form#uploadform').on('beforeSubmit', function(e) {
                var xhr = Service.UploadFile.xhr(document.getElementById('uploadform'));
            }).on('submit', function(e){
                e.preventDefault();
            });

        });

//       $(document).ready(function(){
//           //<input onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
//           $('#ordereditform-mobile').attr('maxlength','12').attr('onkeypress','keyPress()');
//           $('#ordereditform-qq').attr('maxlength','11').attr('onkeypress','keyPress()');
//           $('#ordereditform-total_len').attr('onkeypress','keyPress()');
//           $('#ordereditform-amount').attr('onkeypress','keyPress()');
//           $('#accountsoutcreateform-in_account_number').attr('onkeypress','keyPress()').attr('maxlength','19');
//       });
//
//        //只能输入数字
//        function keyPress(event) {
//            var keyCode = event.keyCode;
//            if ((keyCode >= 48 && keyCode <= 57)){
//                event.returnValue = true;
//            }else{
//                event.returnValue = false;
//            }
//        }


        <?php $this->endBlock(); ?>
    </script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>