<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SeasonUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="season-user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'notes')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'matchpoints')->textInput() ?>

    <?= $form->field($model, 'game_count')->textInput() ?>

    <?= $form->field($model, 'opponent_count')->textInput() ?>

    <?= $form->field($model, 'match_count')->textInput() ?>

    <?= $form->field($model, 'dues')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'season_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
