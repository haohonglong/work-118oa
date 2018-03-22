<?php
/* @var $this \yii\web\View */
/* @var $content string */

$this->title = '月份统计没结算的订单';
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header"><h3>月份统计完成没有结算的订单</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                <tr role="row">
                                    <th>月份</th>
                                    <th>订单数</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($data as $item){?>
                                    <tr role="row">
                                        <td><?= $item['m']?></td>
                                        <td><?= $item['num']?></td>
                                    </tr>
                                <?php }?>


                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
