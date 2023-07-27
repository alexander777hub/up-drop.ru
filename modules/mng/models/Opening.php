<?php

namespace app\modules\mng\models;

use alexander777hub\crop\models\PhotoEntity;
use app\models\Item;
use app\models\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "opening".
 *
 * @property int      $id
 * @property string   $name
 * @property int|null $avatar_id
 * @property int|null $user_id
 * @property User[]   $users
 * @property float    $price
 * @property Item[]   $items
 * @property int|null $category_id
 * @property OpeningCategory|null $openingCategory
 */
class Opening extends \yii\db\ActiveRecord

{
    public function beforeDelete()
    {
        $q = 'DELETE FROM `opening_user` WHERE
                    `opening_user`.`case_id` = ' .(int) $this->id . '
                     ';
        \Yii::$app->db->createCommand($q)->execute();
        $q = 'DELETE FROM `opening_item` WHERE
                    `opening_item`.`case_id` = ' .(int) $this->id . '
                     ';
        \Yii::$app->db->createCommand($q)->execute();
        /*$q = 'INSERT IGNORE INTO item SET profile_id = NULL WHERE
                    `item`.`case_id` = ' .(int) $this->id . ' 
                   '; */

        return parent::beforeDelete();

        // TODO: Change the autogenerated stub

    }

    public $photo;

    public  $item_ids;

    public $user_ids;

    public $category = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opening';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'item_ids'], 'required'],

            [['avatar_id', 'user_id', 'category_id'], 'integer'],
            [['name'], 'string', 'max' => 20],
          //  [['price'], 'validatePrice'],
            [['item_ids'], 'validateCase'],
            [['user_ids', 'item_ids'], 'safe'],
          //  [['price'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/',  'min' => 25.00, 'max' => 999999999.9999],
           [['price'], 'number', 'numberPattern' => '/^[1-9][-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/',  'min' => 25.00, 'max' => 999999999.9999],
        ];
    }


    public function validateCase()
    {
        if(count($this->item_ids) < 10 || count($this->item_ids) > 500){
            $error = "Количество предметов при открытии должно быть от 10 до 500";
            $this->addError('item_ids', $error);
        }
        /*foreach($this->item_ids as $k=>$val){
            $rows = (new \yii\db\Query())
                ->select(['case_id', 'item_id', 'user_id'])
                ->from('opening_item')
                ->where(['user_id' => $this->user_id])
                ->andWhere(['item_id' => intval($val)])
                ->all();
            if($rows){
                $name = Item::findOne(intval($val))->getAttribute('internal_name');
                $error = $name . ':  такой кейс уже существует';
                $this->addError('item_ids', $error );
                \Yii::$app->getSession()->setFlash('danger', $error);
                return;
            }
        } */


    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'avatar_id' => 'Avatar ID',
            'user_id' => 'Пользователь',
            'item_ids' => 'Предметы'
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('opening_user', ['case_id' => 'id']);
    }

    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])
            ->viaTable('opening_item', ['case_id' => 'id']);
    }



    public static function getAvatarUrl($id)
    {
        $photo = PhotoEntity::findOne($id);
        // return a default image placeholder if your source profile_pic is not found
        return $photo && $photo->url ? $photo->url : "/uploads/profile/default.png";
    }

    public static function getOriginal($id)
    {
        if(!$id){
            return 'Не задано';
        }
        $photo = self::getAvatarUrl($id);

        $photo = str_replace('public', 'original', $photo);
        return $photo;

    }

    /**
     * Gets query for [[Photos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(PhotoEntity::className(), ['bind_obj_id' => 'id']);
    }

    public static function getCaseName ($id)
    {
       $name = self::find()->where(['id' => $id]) ? self::find()->where(['id' => $id])->one()->name : null;
       return $name;

    }

    public function setItems()
    {
        if(!empty($this->items)){
            foreach($this->items as $k => $val){
                $this->item_ids[] = $val['id'];
            }
        }
    }

    public function setUsers()
    {
        if(!empty($this->users)){
            foreach($this->users as $k => $val){
                $this->user_ids[] = intval($val['id']);
            }
        }
    }

    public function addItem($id)
    {
        if (!$id) {
            return false;
        }


        $row = (new \yii\db\Query())
            ->select(['case_id', 'item_id', 'user_id', 'id'])
            ->from('opening_item')
            ->where(['item_id' =>(int)$id ])
            ->andWhere(['case_id' =>(int)$this->id ])
            ->andWhere(['price' => $this->price ])
            ->one();
        if($this->user_ids){
            if($row) {
                foreach($this->user_ids as $user_id){
                    \Yii::$app->db->createCommand("UPDATE opening_item SET user_id=:user_id WHERE case_id=:case_id AND item_id=:item_id AND id=:row_id IS NULL AND price=:price",
                        [
                            ':user_id' => $user_id,
                            ':case_id' => $this->id,
                            ':item_id' => $id,
                            ':row_id' => intval($row['id']) ,
                            ':price' => $this->price,
                        ])
                        ->execute();
                }
                return true;

            } else {
                foreach($this->user_ids as $user_id){
                    $q = 'INSERT IGNORE INTO opening_item SET case_id = ' . (int)$this->id . ',
                    item_id=' . (int)$id . ', price=' . (int)$this->price . ', user_id=' . $user_id . '';

                    Yii::$app->db->createCommand($q)->execute();

                }
                return true;

            }
        }
        if($row) {
            \Yii::$app->db->createCommand("UPDATE opening_item SET item_id=:item_id, price=:price WHERE id=:row_id", [
                ':item_id' => $id,
                ':price' => $this->price,
                ':row_id' => intval($row['id']),
            ])
                ->execute();
            return true;
        }


        if(!$row){
            $q = 'INSERT IGNORE INTO opening_item SET case_id = ' . (int)$this->id . ',
                    item_id=' . (int)$id . ', price=' . (int)$this->price . '';


            Yii::$app->db->createCommand($q)->execute();
            return true;
        }
        return false;

    }

    public function updateUsers()
    {
        $i = $this->users;
        if(!$i) {
            return;
        }
        if(!$this->user_ids){
            foreach ($i as $item){
                $rows = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('opening_user')
                    ->where(['user_id' => intval($item['id'])])
                    ->andWhere(['case_id' => intval($this->id)])
                    ->all();
                if($rows){
                   foreach ($rows as $k => $val){
                       $q = 'DELETE FROM `opening_user` WHERE
                            `opening_user`.`id` = ' . intval($val['id']) . '
                             ';
                       \Yii::$app->db->createCommand($q)->execute();
                   }
                   
                }
               
            }
            return;
        }
        $arr = ArrayHelper::map($i, 'id', 'id');
        $values = array_values($arr);
        foreach($this->user_ids as $k => $val){
            $rows = (new \yii\db\Query())
                ->select(['user_id'])
                ->from('opening_user')
                ->where(['user_id' => intval($val)])
                ->andWhere(['case_id' => intval($this->id)])
                ->all();

            if(!$rows){
                $q = 'INSERT IGNORE INTO opening_user SET case_id = ' . intval($this->id) . ',
                    user_id=' . intval($val) . '';


                Yii::$app->db->createCommand($q)->execute();
                continue;
            }

            $r = $rows;

            foreach($arr as $key => $item){
                $id = $item;
                if($val == $item){
                    unset($arr[$key]);
                }
            }

            if(!empty($arr)){
                foreach($arr as $item){
                    $q = 'DELETE FROM `opening_user` WHERE
                            `opening_user`.`user_id` = ' . intval($item) . ' AND  `opening_user`.`case_id` = ' . intval($this->id) . '
                             ';
                    \Yii::$app->db->createCommand($q)->execute();
                }

            }
        }
    }

   public function updateItems()
   {
       if(!$this->item_ids){
           return;
       }
       if(!$this->items){
           return;
       }
       $i = $this->items;

       $arr = ArrayHelper::map($i, 'id', 'id');
       foreach($this->item_ids as $k => $val){
           foreach($arr as $key => $item){
               $id = $item;
               if($val == $item){
                   unset($arr[$key]);
               }

           }
       }


       if($this->user_ids){
           foreach ($this->user_ids as $user_id){
               foreach ($this->item_ids as $itemId){
                   $rows = (new \yii\db\Query())
                       ->select(['id'])
                       ->from('opening_item')
                       ->where(['user_id' => $user_id])
                       ->andWhere(['case_id' => intval($this->id)])
                       ->andWhere(['item_id' => intval($itemId)])
                       ->all();
                   if($rows){
                       if(!empty($arr)){
                           foreach($arr as $item){
                               $q = 'DELETE FROM `opening_item` WHERE
                                `opening_item`.`id` = ' . intval($item) . '
                                 ';
                               \Yii::$app->db->createCommand($q)->execute();

                           }

                       }

                   }
               }
           }

       }


    /*   $a = $arr;
       $p = $this->price;
       foreach ($arr as $item) {
           $q = 'SELECT  `opening_item`.`id` FROM `opening_item` INNER JOIN `opening_user` ON `opening_item`.`user_id`= `opening_user`.`user_id` WHERE
                    `opening_item`.`item_id` = ' . (int)$item . ' AND `opening_item`.`case_id` = ' . (int)$this->id . ' AND `opening_item`.`price` = ' . (int)$this->price . '
                     ';
           $ids = \Yii::$app->db->createCommand($q)->queryAll();

           if (!empty($ids)) {
               foreach ($ids as $val) {
                   $q = 'DELETE FROM `opening_item` WHERE
                    `opening_item`.`id` = ' . intval($val['id']) . '
                     ';
                   \Yii::$app->db->createCommand($q)->execute();
               }
           }
       } */


   }

   protected function updateInternal($attributes = null)
   {
       $a = $attributes;
       return parent::updateInternal($attributes); // TODO: Change the autogenerated stub
   }

    public function getOpeningCategory()
    {
        return $this->hasOne(OpeningCategory::class, ['id' => 'category_id']);
    }

    public function save($runValidation = true, $attributeNames = null)
    {

        /*if (empty($this->item_ids)) {
            if(!empty($this->items)){
                foreach($this->items as $k => $item){
                    $q = 'DELETE FROM `opening_item` WHERE
                    `opening_item`.`case_id` = ' .intval($this->id ). ' AND   `opening_item`.`item_id` = ' . intval($item['id'] ). '
                     ';
                    \Yii::$app->db->createCommand($q)->execute();
                }

            }

        }
        if (empty($this->user_ids)) {
            if(!empty($this->users)){
                foreach($this->users as $k => $user){
                    $q = 'DELETE FROM `opening_user` WHERE
                    `opening_user`.`case_id` = ' .intval($this->id ). ' AND   `opening_user`.`user_id` = ' . intval($user['id'] ). '
                     ';
                    \Yii::$app->db->createCommand($q)->execute();
                }

            }

        } */

        return parent::save($runValidation, $attributeNames); // TODO: Change the autogenerated stub
    }

    public function addUsers($id)
    {
        if (!$id) {
            return false;
        }
        if(!$this->user_ids){

        }

        $row = (new \yii\db\Query())
            ->select(['case_id', 'user_id'])
            ->from('opening_user')
            ->where(['user_id' =>(int)$id ])
            ->andWhere(['case_id' =>(int)$this->id ])
            ->one();
        if(!$row){
            $q = 'INSERT IGNORE INTO opening_user SET case_id = ' . intval($this->id) . ',
                     user_id=' . intval($id) . ' ';


            Yii::$app->db->createCommand($q)->execute();
            return true;
        }
        return false;

    }



 /*   public function afterSave($insert, $changedAttributes)
    {
        $i = $this->items;
        $arr = ArrayHelper::map($i, 'id', 'id');
        foreach($this->item_ids as $k => $val){
            foreach($arr as $key => $item){
                $id = $item;
                if($val == $item){
                    unset($arr[$key]);
                }

            }
        }
        $p = $this->price;
        foreach ($arr as $item) {
            $q = 'SELECT  `opening_item`.`id` FROM `opening_item` INNER JOIN `opening_user` ON `opening_item`.`user_id`= `opening_user`.`user_id` WHERE
                    `opening_item`.`item_id` = ' . (int)$item . ' AND `opening_item`.`case_id` = ' . (int)$this->id . ' AND `opening_item`.`price` = ' . (int)$this->price . '
                     ';
            $ids = \Yii::$app->db->createCommand($q)->queryAll();

            if (!empty($ids)) {
                foreach ($ids as $val) {
                    $q = 'DELETE FROM `opening_item` WHERE
                    `opening_item`.`id` = ' . intval($val['id']) . '
                     ';
                    \Yii::$app->db->createCommand($q)->execute();
                }
            }
        }
        if (!empty($this->item_ids)) {
            foreach ($this->item_ids as $k => $val) {
                $this->addItem(intval($val));
            }
        }

        if (!empty($this->user_ids)) {
                foreach ($this->user_ids as $k => $val) {
                    $this->addUser(intval($val));
                }


        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    } */
}
