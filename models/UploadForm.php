<?php
/**
 * Created by PhpStorm.
 * User: macmini
 * Date: 17/3/31
 * Time: 上午9:21
 */

namespace app\models;


use app\helpers\alioss\OSS;
use yii\base\Model;
use yii\helpers\Html;


class UploadForm extends Model
{
    public $imageFile,$order_id,$title,$created_time,$type;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id','title'],'required'],
            [['imageFile'], 'file', 'extensions' => 'png,jpg,doc,docx,xls,zip,rar','maxSize'=>134217728,'checkExtensionByMimeType'=>false],
        ];
    }

    /**
     * @param null $name
     * @return mixed
     */
    private function getFileType($name=null)
    {
        if($name){
            $arr = explode('.',$name);
            $len = count($arr);
            return $arr[$len-1];
        }
        return '';
    }
    private function createFolder($path)
    {
        $res=mkdir($path,0777,true);
        if (!$res){
            echo "目录 $path 创建失败";
            exit;
        }
    }

    public function upload()
    {
        $files = new Files();
        if ($this->validate()) {
//            $root = \Yii::$app->basePath.'/web/';

            $files->created_time = date('YmdHis');
            $files->uid = \Yii::$app->user->identity->id;
            $files->order_id = $this->order_id;
            $files->title = trim($this->title);
            $path =date('Ymd').'/'.$files->order_id;

            $files->filename = uniqid();
            $path .= '/'.$files->filename;
            $files->type = $this->getFileType($this->imageFile->name);
            $files->path = $path.'.'.$files->type;

            if($files->save()){
                OSS::saveFile($files->path,$this->imageFile->tempName);
                $msg='上传了文件';
                $msg.='创建时间->'.$files->created_time;
                $msg.='文件标题->'.$files->title;
                $msg.='文件路径->'.$files->path;
                Logs::createLog($files->order_id,$msg);
                return true;

            }else{
                $this->addErrors($files->getErrors());
            }


        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'imageFile' => '上传文件',
            'order_id' => '订单ID',
            'title' => '文件标题',
            'filename' => '文件名称',
            'path' => '上传文件路径',
            'created_time' => '创建时间',
        ];
    }
}