<script type="text/javascript">
    <?php $this->beginBlock('js'); ?>
    jQuery(function($){
        $('#dataTable tbody').on('click','tr[data-id]',function(event){
            var self = this;
            var orders = $(this).data('orders');
            if($(self).next('tr').find('table')[0]){

            }else{
                $.post("/report-form/show-detail",{'orders':orders},function(data){
                    if(data){
                        var html = template('dataTable:tp',{'data':data});
                        $(self).after(html).next().show();
                    }
                },'json');
            }


            $('.none').hide();
            $(this).next().show();
            event.stopPropagation();
        });



        $('#dataTable').on('click',function(event){
            event.stopPropagation();
        });

        $('.content').on('click',function () {
            $('.none').hide();
        });

    });

    <?php $this->endBlock(); ?>
</script>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); //将编写的js代码注册到页面底部  ?>
<!-- 转出模板-->
<script id="dataTable:tp" type="text/html">
    <tr class="none">
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
                    <th>利润</th>
                    <th>百分比</th>
                    <th>状态</th>
                    <th>创建日期</th>
                    <th>完成日期</th>
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
                    <td><%= data[i]['profit']%></td>
                    <td><%= data[i]['per']%>%</td>
                    <td><%= data[i]['status']%></td>
                    <td><%= data[i]['created_time']%></td>
                    <td><%= data[i]['finished']%></td>
                </tr>
                <% }%>


                </tbody>

            </table>
        </td>
    </tr>

</script>
<?php if(isset($data) && !empty($data)){?>
    <table id="dataTable" class="table table-bordered table-hover"><thead>
        <tr><th width="10">售前</th><th width="20">总字数</th><th width="30">入账金额</th><th width="30">出账金额</th><th width="30">销售总额(订单金额)</th><th width="30">总利润</th><th width="30">百分比</th></tr>
        </thead>
        <tfoot>
        <tr class="info"><td>总计:</td><td><?=$sum_arr['total_len']?></td><td><?=$sum_arr['cost_in']?></td><td><?=$sum_arr['cost_out']?></td><td><?=$sum_arr['amount']?></td><td><?=$sum_arr['profit']?></td><td>&nbsp;</td></tr>
        </tfoot>
        <tbody>
        <?php foreach ($data as $id =>$item){
            $lassName = '';
            if($item['cost_in'] != 0){
                $per = round($item['profit']/$item['cost_in']*100);
                if($per<=45){
                    $num ='<span class="danger">'.$per.'%</span>';
                    $lassName = 'danger';
                }elseif($per<=50){
                    $num ='<span class="success">'.$per.'%</span>';
                    $lassName = 'success';
                }else{
                    $num ='<span>'.$per.'%</span>';

                }
            }else{
                $num ='<span class="danger">0</span>';
                $lassName = 'danger';
            }
            if($item['profit'] < 0){
                $lassName = 'danger';
            }
            ?>

            <tr class="<?=$lassName?>" data-orders="<?=base64_encode(json_encode($item["orders"]));?>" data-id="<?=$id?>"><td><?=$item['real_name']?></td><td><?=$item['total_len']?></td><td><?=$item['cost_in']?></td><td><?=$item['cost_out']?></td><td><?=$item['amount']?></td><td><?=$item['profit']?></td><td><?=$num?></td></tr>

        <?php }?>
        </tbody>
    </table>
<?php }?>