<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'plugins/datepicker/bootstrap-datetimepicker.css',

        'plugins/daterangepicker/daterangepicker.css',

        'css/dataTables.bootstrap.css',
        'font-awesome/css/font-awesome.css',
        'css/AdminLTE.min.css',
        'css/_all-skins.css',
        'css/index.css',
    ];
    public $js = [
        'plugins/vue/vue.js',
        'plugins/artTemplate/artTemplate.js',
        'js/app.js',
        'js/service.fns.js',
        'js/G_dataBox.js',
        'js/event.js',
        'plugins/slimscroll/jquery.slimscroll.min.js',
        'plugins/datepicker/bootstrap-datetimepicker.js',
        'plugins/datepicker/bootstrap-datetimepicker.zh-CN.js',

        'plugins/daterangepicker/moment.js',
        'plugins/daterangepicker/moment.zh-cn.js',
        'plugins/daterangepicker/daterangepicker.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
