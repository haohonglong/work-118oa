<?php
/* @var $this \yii\web\View */
/* @var $content string */

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




