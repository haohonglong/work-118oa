<?php
namespace app\helpers\alioss;

use OSS\OssClient;

class OSS{
    const OSS_PRIMARY = 'primary';
    static function saveFile($path,$content){
        $object = '118File/'.$path;
        self::store(self::OSS_PRIMARY,$object,$content);

    }
    static function read($path)
    {
        $object = '118File/'.$path;
        $save = self::get(self::OSS_PRIMARY,$object);
        return $save;
    }

    private static function get($oss,$object){
        $server = \Yii::$app->params['alioss'][$oss];
        $ossClient = new OssClient($server['id'],$server['key'],$server['address']);
        $content = $ossClient->getObject($server['bucket'],$object);

        return $content;

    }
    private static function store($oss,$object,$content){
        $server = \Yii::$app->params['alioss'][$oss];

        $ossClient = new OssClient($server['id'],$server['key'],$server['address']);

        $upload_file_options = $content ? $content :__FILE__;
        $ossClient->uploadFile($server['bucket'],$object,$upload_file_options);

    }


}