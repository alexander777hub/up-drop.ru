<?php


namespace app\modules\mng\models;

use alexander777hub\crop\models\File;
use yii\base\Exception;
use Yii;

/**
 * Class ItemPhotoEntity
 *
 * @package app\modules\mng\models
 */
class ItemPhotoEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%item_photo_entity}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bind_obj_id'], 'required'],
            [['bind_obj_id', 'type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['description', 'url'], 'string'],
            [['title'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'bind_obj_id' => Yii::t('app', 'Bind Obj ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'description' => Yii::t('app', 'Description'),
            'title' => Yii::t('app', 'Title'),
            'type' => Yii::t('app', 'Type'),
        ];
    }
    public function movePhoto($file)
    {
        try {
            $filepath = explode('.', $file);
            $ext = $filepath[1];
            $file_first =explode('/', $filepath[0]);
            $index = array_key_last($file_first);
            $a = Yii::getAlias('@webroot');
            $name = $file_first[$index];
            $from = Yii::getAlias('@webroot') . $file;
            $to = Yii::getAlias('@webroot') . File::DIR_PUBLIC . $name . '.' . $ext;
            copy($from, $to);
            if(file_exists($from)){
                unlink($from);
            }
            return File::DIR_PUBLIC . $name . '.' . $ext;
        } catch (Exception $e) {
            Yii::$app->getSession()->setFlash('danger', $e->getMessage());
            return false;
        }
    }
}