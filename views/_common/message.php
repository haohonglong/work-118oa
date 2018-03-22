<div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
        <!-- Messages: style can be found in dropdown.less-->
        <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle animation-shake-slow" data-toggle="dropdown">
                <i class="fa fa-envelope-o"></i>
                <span class="label label-success"><?= $count?></span>
            </a>
            <ul class="dropdown-menu">
                <li class="header">
                    <?php
                    if(\Yii::$app->user->can('p_admin')){
                        $msgStr = "管理员可看所有信息";$type = '0';
                    }else{
                        if(\Yii::$app->user->can('p_accounter')) {
                            $name = '财务';$type = '2';
                        }elseif(\Yii::$app->user->can('p_after_sale')) {
                            $name = '客服';$type = '1';
                        }
                        $msgStr = "{$name}您有 {$count} 条信息";
                    }
                    $readStr = "<span style='margin:0px 0px 0px 10px'><a href='javascript:void(0)' class='read' data-type=".$type.">一键已读</a></span>";
                    echo $msgStr.$readStr;
                    ?>
                </li>
                <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                        <?php foreach ($messages as $k => $item):?>
                        <li><!-- start message -->
                            <a href="<?=\yii\helpers\Url::toRoute(['order/read-message','id'=>$item['id'],'order_id'=>$item['order_id']])?>">
                                <p style="margin-left: 0;">订单号: <?= $item['order_id']?>  <?= $item['content']?> </p>
                            </a>
                        </li>
                        <!-- end message -->
                        <?php endforeach;?>
                    </ul>
                </li>
            </ul>
        </li>

    </ul>
</div>