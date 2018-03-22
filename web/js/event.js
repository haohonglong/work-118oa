/**
 * Created by lhh on 17/3/24.
 */
if('undefined' === typeof Service){throw new Error('warning: 没有service.fns.js 文件 ,或加载顺序不对');}
if('undefined' === typeof G_dataBox){throw new Error('warning: 没有G_dataBox.js 文件 ,或加载顺序不对');}
//全局事件
jQuery(function($){
    $('.js_gotoback').on('click',function(e){
        Service.goToBack(e);
    });
});
//财务管理
jQuery(function($){
    var $accountinput_id = $('#accountseditform-ids');
    var $accountModal =$('#accountModal');
    //模态框显示时找对应的勾选ID 并存取到它里面的隐藏框里
    $('button[data-target="#accountModal"]').on('click',function () {
        var $tr = $(this).closest('.box').find('.account-table tr');
        $accountinput_id.val(Service.getIdBySelectedCheckBox($tr));
        if($accountinput_id.val().length < 1){
            $(this).attr('data-target','');
            alert('请勾选一个或多个账单');
            return true;
        }
        $(this).attr('data-target','#accountModal');
    });
    //日历范围选择器
    $('.daterange').daterangepicker({
        locale: {
            applyLabel: '确定',
            cancelLabel: '取消',
            format: 'YYYY-MM-DD'
        }
    }).on('cancel.daterangepicker', function(ev, picker) {
        $('.daterange').val('');
    });


    //点击全选checkbox 时,执行全选功能
    $('table thead').on('click','input.account_all',function(){
        var $list = $(this).closest('.box').find('.account-table tr input[type="checkbox"]');
        Service.checkboxSelectAll($list,this);
        Service.countAmount($list,this,$(this).closest('.box').find('.amount-count'));
    });

    //财务管理点击每一个多选框
    $('table .account-table').on('click','input[type=checkbox]',function(){
        var n=0,count=0;
        var $tr = $(this).closest('tr');
        var $box = $tr.closest('.box').find('.amount-count');
        var allcheckbox = $tr.closest('.box').find('table thead input.account_all')[0];
        count = parseFloat($box.text());
        count = $.isNumeric(count) ? count : 0;
        n = $tr.find('td:eq(1)').text();
        n = parseFloat(n);
        if(this.checked){
            count += n;
        }else{
            allcheckbox.checked=false;
            count -= n;
        }
        $box.text(count);
    });


});