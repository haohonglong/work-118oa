/**
 functions
 */
if(!Service) {var Service = {};}
if(!Service.UploadFile) {Service.UploadFile = {};}
if(!Service.Push) {Service.Push = {};}
//扩充 artTemplate 方法
(function(){
    "use strict";
    template.helper('parseFloat', parseFloat);


})();

(function() {
    "use strict";

    /**
     * 功能：回退
     * @param e
     */
    Service.goToBack=function (e) {
        e.preventDefault();
        window.history.go(-1);
    };
    /**
     *
     * 功能：多选框全选反选，默认为反选状态，输入chexkbox 才可改变全选状态
     * @param {jQuery}$list     所有的checkbox
     * @param {Node}checkbox    当前所点全选的checkbox
     */
    Service.checkboxSelectAll=function($list,checkbox) {
        $list.each(function(){
            var _this=this;
            if(checkbox){
                _this.checked=checkbox.checked;
            }else{
                _this.checked=!_this.checked;
            }
        });
    };
    /**
     * 统计账单的金额且计算后的数字放到指定的容器内
     * @param $list
     * @param checkbox
     * @param $box
     */
    Service.countAmount=function($list,checkbox,$box) {
        var count =0,n=0;
        if(checkbox.checked) {
            $list.each(function () {
                n = $(this).closest('tr').find('td:eq(1)').text();
                count += parseFloat(n);
            });
        }
        $box.text(count);
    };

    /**
     *根据对应元素里选择的checkbox 获取对应的id
     * @param $list
     * @returns {string}
     */
    Service.getIdBySelectedCheckBox=function($list){
        var ids=[];
        $list.each(function (i) {
            var $checkbox =$(this).find('input[type="checkbox"]');
            if($checkbox.is(':checked')){
                ids.push($(this).data().id);
            }
        });
        return ids.join(',');
    };
    /**
     *
     * @param href
     */
    Service.setMenuSelectedStatus=function(href){
        $('.sidebar-menu a[href="'+href+'"]')
            .closest('li').addClass('active')
            .closest('.treeview-menu').show()
            .closest('li').addClass('treeview active');
    };
    Service.getLogs=function () {};
    Service.getListData=function () {};
    //处理表单功能
    Service.from = {
        /**
         *清空表单里的内容
         * @param $form
         * @param arr   不需要清空,要保留的name值
         */
        'clearInputVal':function ($form,arr) {
            var $input = $form.find("[name]");
            $input.each(function(i){
                if(-1 === $.inArray(i,arr || [0,1])){
                    $(this).val('')

                }
            });
        }
    };
    /**
     * 解析文件格式
     * @param url
     * @returns {string}
     */
    Service.parseFileType=function(type){
        switch (type){
            case 'doc':
            case 'docx':
                type = 'word文档';
                break;
            case 'xls':
                type = '电子表格';
                break;
            case 'zip':
            case 'rar':
                type = '压缩文件';
                break;
            case 'jpg':
            case 'png':
                type = '图片';
                break;


        }
        return type;
    };





})();
/**
 * 上传文件进度条
 */
(function(){
    "use strict";
    var $modeal,$progress,$progress_num,$progress_bar;
    function uploadProgress(evt) {
        if (evt.lengthComputable) {
            var percentComplete = Math.round(evt.loaded * 100 / evt.total);
            $progress.show();
            var num =percentComplete.toString() + '%';
            $progress_bar.width(num);
            $progress_num.text(num);
        }else {
        }
    }
    function uploadComplete(event) {
        /* This event is raised when the server send back a response */
        var data = JSON.parse(event.target.responseText);
        if(1 == data.status){
            $progress_num.text('上传完成!');
            $progress_bar.width('100%');
            location.href = data.url;
        }else{
            alert('无法解析您上传的文件');
        }
        $progress.hide();
        $progress_bar.width(0);

    }

    /**
     * 生成xhr 对象
     * @param dom
     * @returns {XMLHttpRequest}
     */
    Service.UploadFile.xhr=function(dom){
        var xhr = new XMLHttpRequest();
        var fd = new FormData(dom);
        xhr.upload.addEventListener("progress", uploadProgress, false);
        xhr.addEventListener("load", uploadComplete, false);
        xhr.open("POST", dom.action);
        xhr.send(fd);
        xhr.responseType = "";
        return xhr;
    };
    /**
     * onload时初始化
     */
    Service.UploadFile.run=function(){
        $modeal = $('#uploadFileModal');
        $progress = $modeal.find('.progress');
        $progress_num = $modeal.find('.progress .sr-only');
        $progress_bar = $modeal.find('.progress .progress-bar');

    };
})();
//服务器推
(function () {
    function push() {

    }
   Service.Push.run=function(){};
})();

jQuery(function($){
    //主菜单选择状态
    (function(){
        function returnUrl(){
            var url1,url2,
                url = window.location.pathname;
            url=url.split('/');
            url1 = '/'+url[1];
            url2 = '/'+url[1]+'/'+url[2];
            return [url1,url2];
        }

        var href=returnUrl();
        var $href=$('.sidebar-menu a[href="'+href[0]+'"]');
        if(0 === $href.length){
            $href=$('.sidebar-menu a[href="'+href[1]+'"]');
        }
        $href
            .closest('li').addClass('active')
            .closest('.treeview-menu').show()
            .closest('li').addClass('treeview active');
    })();

});