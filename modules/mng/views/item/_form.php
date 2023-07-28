<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Item $model */
/** @var yii\widgets\ActiveForm $form */
\app\assets\JQAsset::register($this);

?>


<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'internal_name')->textInput() ?>
    <?= $form->field($model, 'rarity')->textInput() ?>
    <?= $form->field($model, 'exterior')->textInput() ?>
    <?= $form->field($model, 'icon_url')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'internal_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true, 'readOnly' => true]) ?>
    <img src=<?php $model->icon_url ? 'https://community.cloudflare.steamstatic.com/economy/image/' . $model->icon_url . '/image.png ' : '/uploads/profile/default.png' ?>>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
