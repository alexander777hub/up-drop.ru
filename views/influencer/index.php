<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\InfluencerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Influencers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="influencer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Influencer'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],


            'id',
            'username',
            'platform',
            'status',
            'audience',
            //'engagement_rate',
            //'user_id',
            [
                'class' => ActionColumn::className(),
                'template'=> '{view}{delete}',
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
