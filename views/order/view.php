<?php
/* @var $this \yii\web\View */
/* @var $content string */

use \yii\widgets\ActiveForm;
use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title = '订单';


?>


    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            <h4>订单编号:<?= $model->order_id?> <small class="font-blue" style="margin-left: 10px;">状态:<?= $workflow?></small></h4>
                            <button class="btn btn-info js_gotoback">返回</button>
                        </div>
                    </div>
                    <div class="box-body">

                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">客户名称:</label>
                                    <span class="ML10"><?=$model->guest_name?></span>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">论文标题:</label>
                                    <span class="ML10"><?=$model->title?></span>
                                </div>

                                <?php if(\Yii::$app->user->can('p_accounter') || \Yii::$app->user->can('p_leader')){?>
                                <div class="form-group">
                                    <label class="control-label">总利润:</label>
                                    <span class="ML10" id="profit"><?= $model->profit?></span>
                                </div>
                                <?php }?>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">联系方式:</label>
                                    <span class="ML10"><?=$model->mobile?></span>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">QQ:</label>
                                    <span class="ML10"><?=$model->qq?></span>
                                </div>

                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">总字数:</label>
                                    <span class="ML10"><?=$model->total_len?></span>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">订单金额:</label>
                                    <span class="ML10"><?=$model->amount?></span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">类型:</label>
                                    <span class="ML10"><?=$model->type?></span>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">约定时间:</label>
                                    <span class="ML10"><?=$model->appointed_time?></span>
                                </div>

                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">售后客服:</label>
                                    <span class="ML10"><?=$model->after_sale?></span>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">上传文件</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive no-padding">
                                        <table class="table table-hover table-account">
                                            <thead>
                                            <tr>
                                                <th width="75%">文件标题</th>
                                                <th width="">是否有效</th>
                                                <th width="">文件类型</th>
                                                <th>创建时间</th>
                                                <th width="1%">操作</th>
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
                        <?php $form= ActiveForm::begin([
                            'action' => ['order/edit-note'],
                        ])?>
                        <input type="hidden" name="order_id" value="<?= $model->order_id?>">
                        <div class="row MB20">
                            <div class="col-sm-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">订单备注</h3>
                                        <div class="box-tools">
                                            <?= Html::submitButton('修改', ['class' => 'btn btn-primary']) ?>
                                        </div>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body PL25">
                                        <textarea name="note" class="form-control" rows="12"><?=$model->note?></textarea>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end();?>


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



    <!-- 转入出模板-->
    <script id="account:tp" type="text/html">
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
        </tr>
        <% }%>
    </script>



    <script type="text/javascript">
        <?php $this->beginBlock('js'); ?>

        Service.getLogs=function (){
            $.post("<?= Url::toRoute(['/log/show-logs'])?>",{'order_id':'<?= $model->order_id?>'},function(data){
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
            $.post("<?= Url::toRoute(['/account/show-account-list'])?>",{'account_type':account_type,'order_id':'<?= $model->order_id?>'},function(data){
                if (data.status == 1){
                    $('#'+name).html(template('account:tp',{'data':data.data,'account_type':account_type}));
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


            Service.getFilesList();

            function main(){
                account_in = 'account_in';
                account_out = 'account_out';
                Service.getLogs();
                Service.getListData(account_in,1);
                Service.getListData(account_out,2);
            }

            main();


        });

        <?php $this->endBlock(); ?>
    </script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>