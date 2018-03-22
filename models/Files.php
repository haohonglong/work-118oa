<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $order_id
 * @property string $title
 * @property string $type
 * @property string $filename
 * @property string $path
 * @property integer $valid
 * @property string $created_time
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_id'], 'required'],
            [['uid', 'order_id'], 'integer'],
            [['title', 'filename'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 8],
            [['path'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => '用户ID',
            'order_id' => '订单ID',
            'title' => '文件标题',
            'filename' => '文件名称',
            'path' => '上传文件路径',
            'type' => '上传文件类型',
            'valid' => '上传文件是否有效',
        ];
    }
}
