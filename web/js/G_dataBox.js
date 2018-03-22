/**
 * Created by haohonglong on 16/11/8.
 */
if(typeof G_dataBox != 'undefined'){throw new Error('warning: G_dataBox.js 文件已加载过,或 G_dataBox 变量已被定义过');}
if('undefined' === typeof Service){throw new Error('warning: 没有service.fns.js 文件 ,或加载顺序不对');}
G_dataBox=(function($){
    "use strict";
    var G_dataBox={},
        error =function(callback){
            return function(){
                var data={
                    'status': false,
                    'msg': '请求数据失败'
                };
                callback(data);
            };
        };

    /**
     *
     * @param s
     * @returns {boolean}
     */
    function is_json(s){
        if(s && ('string' === typeof s) && s.match("^\{(.+:.+,*){1,}\}$")){
            return true;
        }
        return false;
    }


    function filter(data){

    }



    G_dataBox.Order={
        /**
         * 添加转账信息
         * @param Obj
         * @param callback
         */
        'saveAccount':function (Obj,callback){
            Obj = Obj || {};
            $.ajax({
                url:Obj.url,
                data:{
                    pay_type:Obj.pay_type,
                    order_id:Obj.order_id,
                    pay_time:Obj.pay_time,
                    account_type:Obj.account_type,
                    amount:Obj.amount,
                    note:Obj.note
                },
                type:"POST",
                dataType:"json",
                success:function(data){
                    callback(data);
                },
                error:error(callback)
            });
        }


    };

    G_dataBox.UploadFile={
        /**
         * 设置文件有效或无效
         * @param Obj
         * @param callback
         */
        'valid':function (Obj,callback){
            Obj = Obj || {};
            $.ajax({
                url:'/upload-file/valid',
                data:{
                    'order_id':Obj.order_id,
                    'id':Obj.id,
                    'valid':Obj.valid
                },
                type:"POST",
                dataType:"json",
                success:function(data){
                    callback(data);
                },
                error:error(callback)
            });
        }


    };




    return G_dataBox;
})(jQuery);
