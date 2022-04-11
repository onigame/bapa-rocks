<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Season;

/* @var $this yii\web\View */
/* @var $model app\models\Season */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="season-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'previous_season_id')->dropDownList(
         ArrayHelper::map(Season::find()->all(),'id','name'),
         ['prompt' => 'Select Season']
    ) ?>

    <?= $form->field($model, 'playoff_qualification')->dropDownList(
         [4 => 4, 5 => 5]
    ) ?>

    <?= $form->field($model, 'regular_season_length')->dropDownList(
         [10 => 10, 12 => 12]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
