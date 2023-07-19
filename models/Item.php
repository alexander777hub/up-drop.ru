<?php

namespace app\models;

use alexander777hub\crop\models\PhotoEntity;
use app\modules\mng\models\ItemPhotoEntity;
use app\modules\mng\models\Opening;
use Yii;

/**
 * This is the model class for table "item".
 *
 * @property int $id
 * @property int $app_id
 * @property int|null $class_id
 * @property int $currency
 * @property string|null $icon
 * @property string|null $icon_large
 * @property string|null $internal_name
 * @property  int|null $profile_id
 * @property  Profile $profile
 * @property  int $scin_type
 * @property Opening[] $openings
 */
class Item extends \yii\db\ActiveRecord
{
    public function beforeDelete()
    {
        $q = 'DELETE FROM `opening_item` WHERE
                    `opening_item`.`item_id` = ' .(int) $this->id . '
                     ';
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public static function getFullList()
    {
        $arr = \yii\helpers\ArrayHelper::map(\app\models\Item::find()->all(), 'id', 'internal_name');
        $arr[0] = 'Не выбран';
        ksort($arr);
        return $arr;
    }

    public $photo;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['app_id', 'class_id', 'currency', 'profile_id', 'scin_type'], 'integer'],
            [['icon', 'icon_large', 'photo', 'internal_name',], 'string', 'max' => 255],
        ];
    }

    public static function getScins()
    {
        return [
            0 => 'Не выбрано',
            1 => 'Blue',
            2 => 'Violet',
            3 => 'Pink',
            4 => 'Red',
            5 => 'Gold'

        ];
    }



    /**
     * Gets query for [[Photos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(ItemPhotoEntity::className(), ['bind_obj_id' => 'id']);
    }

    public function getOpenings() {
        return $this->hasMany(Opening::className(), ['id' => 'case_id'])
            ->viaTable('opening_item', ['item_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_id' => 'App ID',
            'class_id' => 'Class ID',
            'currency' => 'Currency',
            'icon' => 'Icon',
            'icon_large' => 'Icon Large',
        ];
    }

    public static function getOriginal($icon)
    {
        if(!$icon){
            return false;

        }
        $original =  str_replace('public', 'original', $icon);
        return $original;
    }
}