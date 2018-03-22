<?php
/**
 * Created by PhpStorm.
 * User: haohonglong
 * Date: 17/3/31
 * Time: 下午2:37
 */

namespace app\controllers;

use yii;
use app\helpers\alioss\OSS;
use app\models\Files;
use app\models\Logs;
use app\models\UploadForm;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\helpers\Url;

class UploadFileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' =>[
                    ['allow' => true,'roles' => ['@']]
                ]
            ],
        ];
    }
    public function actionUpload()
    {
        if(\Yii::$app->request->isPost){
            $upload = new UploadForm();
            $upload->load(\Yii::$app->request->post());
            $upload->imageFile = UploadedFile::getInstance($upload, 'imageFile');
            if ($upload->upload()) {
                $var=[
                    'status'=>1,
                    'url'=>Url::to(['/order/edit','id'=>\Yii::$app->request->post('order_id')]),
                    'msg'=>'上传成功',
                ];
            }else{
                $var=[
                    'status'=>0,
                    'msg'=>'上传失败',
                ];
            }
            echo json_encode($var);

        }
    }


    /**
     * 下载文件
     */
    public function actionDownload($id)
    {
        $session = Yii::$app->session;
        if (!$session->isActive){$session->open();}
        $query = Files::findOne($id);
        $filename = $query->title . '.' . $query->type;
        $session->set('uploadfilepath', $query->path);
        $session->set('uploadfilename', $filename);
        $content = $this->readFile();
        if($content){
            $msg ='下载了文件';
            $msg .=$query->title.'.'.$query->type;
            Logs::createLog($query->order_id,$msg);
        }


    }

    private function readFile()
    {
        ini_set('memory_limit','300M');
        $session = Yii::$app->session;
        if (!$session->isActive){$session->open();}
        $path = $session->get('uploadfilepath');
        $filename = $session->get('uploadfilename');
        $content = OSS::read($path);
        if($content){
            \Yii::$app->response->sendContentAsFile($content, $filename);
        }
        //        \Yii::$app->response->sendFile($path, $filename);
        return $content;
    }

    /**
     * 获取所有文件
     */
    public function actionList()
    {
        if(\Yii::$app->request->isAjax){
            $post = \Yii::$app->request->post();
            $query = Files::find()->select('title,filename,path,created_time')->orderBy('created_time DESC')->where(['order_id'=>$post['order_id']])->asArray()->all();
            if($query){
                $var=[
                    'status'=>1,
                    'msg'=>'数据获取成功',
                    'list'=>$query,
                ];
            }else{
                $var=[
                    'status'=>0,
                    'msg'=>'数据获取失败',

                ];
            }
            echo json_encode($var);
        }
    }

    /**
     *
     * 设置上传的文件是否有效,1:有效；0:无效
     */
    public function actionValid()
    {
        $request = Yii::$app->request;
        if(Yii::$app->request->isAjax){
            $id= $request->post('id');
            $valid= $request->post('valid');
            $order_id= $request->post('order_id');
            $query = Files::findOne($id);
            if(1 == $valid){//设置有效
                $query->valid=1;
            }else{
                $query->valid=0;
            }
            if ($query->save()){
                if(1 == $valid){//设置有效
                    Logs::createLog($order_id,'文件已设置有效');
                }else{
                    Logs::createLog($order_id,'文件已设置无效');
                }
                $var=[
                    'status'=>1,
                    'msg'=>'成功',
                ];
            }else{
                $var=[
                    'status'=>0,
                    'msg'=>'失败',
                    'errors'=>$query->getErrors(),
                ];

            }
            echo json_encode($var);
        }
    }
}