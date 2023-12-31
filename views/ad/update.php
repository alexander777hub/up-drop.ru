<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Ad */

$this->title = Yii::t('app', 'Обновить объявление: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Обновить');
?>


<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

