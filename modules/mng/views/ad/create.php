<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Ad */

$this->title = Yii::t('app', 'Пригласить на свидание');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
