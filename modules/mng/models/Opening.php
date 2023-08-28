<?php

namespace app\modules\mng\models;

use alexander777hub\crop\models\PhotoEntity;
use app\models\Item;
use app\models\Profile;
use app\models\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "opening".
 *
 * @property int      $id
 * @property string   $name
 * @property int|null $avatar_id
 * @property User[]   $users
 * @property float    $price
 * @property Item[]   $items
 * @property int|null $category_id
 * @property Item[]   $initItems
 * @property OpeningCategory|null $openingCategory
 */
class Opening extends \yii\db\ActiveRecord

{
     const RARITY_COMMON_WEAPON = 'Rarity_Common_Weapon';

    const RARITY_MYTHICAL = 'Rarity_Mythical';


    const RARITY_LEGENDARY = 'Rarity_Legendary';


    const RARITY_ANCIENT = 'Rarity_Ancient';


    const RARITY_ANCIENT_WEAPON = 'Rarity_Ancient_Weapon';


    const RARITY_RARE_WEAPON = 'Rarity_Rare_Weapon';


    public static function getRarityList()
    {
        return [
            [
                'name' => 'Rarity_Rare_Character',
                'chance' =>  0.023,
            ],
            [
                'name' => 'Rarity_Common',
                'chance' => 79.98,
            ],
            [
                'name' => 'Rarity_Common_Weapon',
                'chance' => 79.98,
            ],
            [
                'name' => 'Rarity_Mythical',
                'chance' => 14.27
            ],
            [
                'name' => 'Rarity_Legendary',
                'chance' =>  2.9
            ],
            [
                'name' => 'Rarity_Ancient',
                'chance' => 0.50
            ],
            [
                'name' => 'Rarity_Ancient_Weapon',
                'chance' => 0.23
            ],
            [
                'name' => 'Rarity_Rare_Weapon',
                'chance' => 0.023
            ],
        ];

    }

    public static function getExteriorList()
    {
        return [
            [
                'name' => 'Battle-Scarred',
                'chance' => mt_rand(45, 100),
            ],
            [
                'name' => 'Well-Worn',
                'chance' => mt_rand(38, 45)
            ],
            [
                'name' => 'Field-Tested',
                'chance' =>  mt_rand(15,38)
            ],
            [
                'name' => 'Minimal Wear',
                'chance' => mt_rand(7,15)
            ],
            [
                'name' => 'Factory New',
                'chance' => mt_rand(0,7)
            ],
        ];

    }
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
        $q = 'DELETE FROM `opening_item_init` WHERE
                    `opening_item_init`.`case_id` = ' .(int) $this->id . '
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
    public $test;

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

            [['avatar_id', 'category_id'], 'integer'],
            [['name'], 'string', 'max' => 20],
          //  [['price'], 'validatePrice'],
        //    [['item_ids'], 'validateCase'],
            [['user_ids', 'item_ids', 'test'], 'safe'],
          //  [['price'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/',  'min' => 25.00, 'max' => 999999999.9999],
           [['price'], 'number', 'numberPattern' => '/^[1-9][-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/',  'min' => 25.00, 'max' => 999999999.9999],
        ];
    }


    public function validateCase()
    {
        if(count($this->item_ids) < 10 || count($this->item_ids) > 20){
            $error = "Количество предметов при открытии должно быть от 10 до 500";

        //    $this->addError('item_ids', $error);
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
            'item_ids' => 'Предметы',
             'user_ids' => 'Юзеры'
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

    public function getInitItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])
            ->viaTable('opening_item_init', ['case_id' => 'id']);
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
        if(!$id) {
            return 'Выпал с апгрейда';
        }
       $name = self::find()->where(['id' => $id]) ? self::find()->where(['id' => $id])->one()->name : null;
       return $name;

    }

    public function setItems()
    {
        if(!empty($this->initItems)){
            foreach($this->initItems as $k => $val){
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
            if($this->user_ids){
                foreach ($this->user_ids as $id){
                    $q = 'INSERT IGNORE INTO opening_user SET case_id = ' . intval($this->id) . ',
                     user_id=' . intval($id) . ' ';

                    Yii::$app->db->createCommand($q)->execute();

                }
            }

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
        foreach ($arr as $key => $item) {
            if (!in_array(strval($item), $this->user_ids)) {
                $q = 'DELETE FROM `opening_user` WHERE
                            `opening_user`.`user_id` = ' . intval($item) . ' AND  `opening_user`.`case_id` = ' . intval($this->id) . '
                             ';
                \Yii::$app->db->createCommand($q)->execute();
            }
        }

        foreach ($this->user_ids as $k => $val) {
            if (in_array(intval($val), $values)) {
                continue;
            } else {
                $q = 'INSERT IGNORE INTO opening_user SET case_id = ' . intval($this->id) . ',
                     user_id=' . intval($val) . ' ';

                Yii::$app->db->createCommand($q)->execute();
            }

            $v = $values;

        }
    }

   public function updateItems($price = null, $winner_id = null, $user_winner_id =null)
   {

       $insertPrice = $price ? $price : $this->price;


       $i = $this->initItems;
       if(!$i) {
           if($this->item_ids){
               foreach ($this->item_ids as $id){
                   $copy = OpeningItemInit::find()->where(['item_id'=> intval($id), 'case_id' => $this->id])->one();
                   $q = 'INSERT IGNORE INTO opening_item_init SET case_id = ' . intval($this->id) . ',
                     item_id=' . intval($id) . ', price =' . $copy->price . '';


                   Yii::$app->db->createCommand($q)->execute();


               }
           }

           return;
       }

       if(!$this->item_ids){

           foreach ($i as $item){
               $q = 'DELETE FROM `opening_item_init` WHERE
                    `opening_item_init`.`item_id` = ' . intval($item->id) . ' AND `opening_item_init`.`case_id` = ' . intval($this->id) . '
                     ';
               \Yii::$app->db->createCommand($q)->execute();
               $q = 'DELETE FROM `opening_item` WHERE
                    `opening_item`.`item_id` = ' . intval($item->id) . ' AND `opening_item`.`case_id` = ' . intval($this->id) . '
                     ';
               \Yii::$app->db->createCommand($q)->execute();



           }
           return;
       }
       $arr = ArrayHelper::map($i, 'id', 'id');
       $values = array_values($arr);
       foreach ($arr as $key => $item) {
           if (!in_array(strval($item), $this->item_ids)) {
               $q = 'DELETE FROM `opening_item_init` WHERE
                            `opening_item_init`.`item_id` = ' . intval($item) . ' AND  `opening_item_init`.`case_id` = ' . intval($this->id) . '
                             ';
               \Yii::$app->db->createCommand($q)->execute();
               $q = 'DELETE FROM `opening_item` WHERE
                            `opening_item`.`item_id` = ' . intval($item) . ' AND  `opening_item`.`case_id` = ' . intval($this->id) . '
                             ';
               \Yii::$app->db->createCommand($q)->execute();
           }
       }
       $query = (new \yii\db\Query())->select(['id'])->from('opening_user')->where(['case_id' => $this->id]);
       $raw = $query->createCommand()->getRawSql();
       $command = $query->createCommand();
       $data = $command->queryAll();
       $ids = [];
       if(!empty($data)){
           foreach ($data as $key => $item){
               $ids[ intval($item['id'])] = intval($item['id']);
           }
       }
       $values_users = !empty($ids) ? $ids : [];
       foreach ($this->item_ids as $k => $val) {
           if(!in_array(intval($val), $values)){
               $q = 'INSERT IGNORE INTO opening_item_init SET case_id = ' . intval($this->id) . ',
                     item_id=' . intval($val) . ' ';
               Yii::$app->db->createCommand($q)->execute();
           }
           if($this->user_ids){
               if($val == $winner_id){
                   foreach($this->user_ids as $k=>$user_id){
                       if(!in_array(intval($user_id), $values_users)){
                           $query = (new \yii\db\Query())->select(['id'])->from('opening_item')->where(['case_id' => $this->id, 'item_id' => intval($val), 'user_id' => intval($user_id), 'price'=> $insertPrice]);

                           $command = $query->createCommand();
                           $data = $command->queryAll();
                           if(!empty($data)){
                               continue;
                           }
                           if($user_winner_id && $user_id != $user_winner_id){
                               continue;
                           }
                           $q = 'INSERT IGNORE INTO opening_item SET case_id = ' . intval($this->id) . ',
                            item_id=' . intval($val) . ', user_id = ' . intval($user_id) . ', price = ' . $insertPrice .  '';
                //           $q = 'REPLACE  INTO opening_item  (case_id, item_id, user_id, price) VALUES (' . intval($this->id) . ',
                //        ' . intval($val) . ',  ' . intval($user_id) . ', ' . $insertPrice .  ')';

                           Yii::$app->db->createCommand($q)->execute();
                           break;
                       }
                   }
               }


           }
          /* if (in_array(intval($val), $values)) {
               continue;
           } else {


           }

           $v = $values; */

       }


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




    public function winItemBy($list)
    {
        $items = [];
        $i = 0;
        $maxTickets = 0;

        foreach ($list as $item) {
            if ($item['chance'] === 0) continue;

            if ($i == 0) {
                $from = 1;
            } else {
                $from = $items[$i - 1]['to'] + 1;
            }

            $to = $from + $item['chance'];
            $maxTickets = $to;

            $items[$i] = [
                'from' => $from,
                'to' => $to,
                'name' => $item['name']
            ];

            $i++;
        }

        try {
            $winTicket = mt_rand(1, $maxTickets);
        } catch (\Exception $e) {
            return null;
        }

        $winItem = null;

        foreach ($items as $item) {
            if ($item['from'] <= $winTicket && $item['to'] >= $winTicket) {
                $winItem = $item;
                break;
            }
        }
        if(!$winItem){
           return 'Rarity_Common_Weapon';
        }

        return $winItem['name'];

    }



    public function open()
    {
        if(!$this->initItems){
            return false;

        }
        $query = (new \yii\db\Query())->select(['item_id'])->from('opening_item_init')->where(['case_id' => $this->id]);
        $command = $query->createCommand();
        $data = $command->queryAll();

        $arr_ids = [];
        foreach($data as $val){
            $arr_ids[] = intval($val['item_id']);
        }
        //rarity_list



        $query = (new \yii\db\Query())->select(['*'])->from('item')->where(['id'=> $arr_ids]);


        $q = $query->createCommand()->getRawSql();
        $command = $query->createCommand();
        $data = $command->queryAll();
        foreach($data as $val){
           $rarity_list[] = [
               'hash_name' => $val['market_hash_name'],
               'name' => $val['rarity'],
                'chance' => 0.00
           ];
        }
        foreach($rarity_list as $k=>&$val){
            foreach(self::getRarityList() as $key => $value){
                if($val['name'] == $value['name']){
                    if(strpos($val['hash_name'], 'StatTrak')) {
                        $val['chance'] = 0.023;
                        continue;
                    }
                    $val['chance'] = $value['chance'];
                }
            }
        }

        $winItemByRarity = $this->winItemBy($rarity_list);
        $winItemByExterior = $this->winItemBy(self::getExteriorList());

        $w = $winItemByRarity;
        $e = $winItemByExterior;
        try {
            $query = (new \yii\db\Query())->select(['*'])->from('item')->where(['rarity' => $winItemByRarity])->andWhere(['id'=> $arr_ids]);
            $q = $query->createCommand()->getRawSql();
            $command = $query->createCommand();
            $data = $command->queryAll();

        } catch(\Exception $exception) {
            $err = $exception;
            echo "RESTART1";
            var_dump($err);
            var_dump($e);
            var_dump($w);
            exit;
        }
        if(!empty($data)){
            if(count($data) > 1) {
                $winner_item = $data[mt_rand(0, (count($data)-1))];
            } else {
                $winner_item = $data[0];
            }
            $winner_id_pos = array_search(intval($winner_item['id']), $arr_ids);

            $winner_id = $arr_ids[$winner_id_pos];

            $item = Item::findOne($winner_id);

            $client = new \GuzzleHttp\Client([
                'timeout' => 60,
                'debug' => false,
            ]);
            $request = $client->request('GET', 'https://market.csgo.com/api/v2/prices/RUB.json', [

                'timeout' => 120,
            ]);
            //

            $r =  json_decode($request->getBody()->getContents(), true);
            if(isset($r['items']) && !empty($r['items'])) {
                $arr = [];
                foreach ($r['items'] as $k=>$val){
                    if(isset($val['market_hash_name']) && $val['market_hash_name'] == $item->market_hash_name){
                        $arr[] = $val['price'];

                    }
                }

                if(!empty($arr)){
                    ksort($arr);
                    $i = 0;
                    if(!isset($arr[$i])){
                        echo "NO data IN item" . '' . $item->id . '<br>';
                        var_dump($arr);
                        exit;
                    }

                } else {
                    echo "NO data for item" . '' . $item->market_hash_name . '<br>';
                }
            }
            $price = $arr[0];
            $this->setUsers();
            $this->setItems();
            $this->user_ids[] = intval(Yii::$app->user->id);

            $this->updateUsers();
            $this->updateItems($price, $winner_id, Yii::$app->user->id);
            $profile = Profile::find()->where(['user_id'=> Yii::$app->user->id])->one();

            $profile->credit = $profile->credit - $this->price;
            $profile->save();
            return [
                'item_id' => $winner_id,
                'user_id' => intval(Yii::$app->user->id),
                'icon_url' => $item->icon_url,
                'price' => $price,
                'market_hash_name' => $item->market_hash_name,
                'rarity' =>  $item->rarity,
                'credit' => round($profile->credit, 2)

            ];

        }
        var_dump($winItemByRarity);
        var_dump($winItemByExterior);
        $this->open();

    }



    public  function getFullListSelect2Filtered()
    {

        $items = \app\models\Item::find()->asArray()->all();
        $caseItems = $this->getItems()->asArray()->all();

        foreach($items as $k=>$val){
            if(!$val['price']){
                unset($items[$k]);
            }
            foreach ($caseItems as $ci){
                if($val['id'] == $ci['id']){
                    unset($items[$k]);
                }
            }

        }
        $arr = \yii\helpers\ArrayHelper::map($items, 'id', 'internal_name');
        return $arr;
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
