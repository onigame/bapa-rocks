<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MatchUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="match-user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'starting_playernum')->textInput() ?>

    <?= $form->field($model, 'matchrank')->textInput() ?>

    <?= $form->field($model, 'game_count')->textInput() ?>

    <?= $form->field($model, 'opponent_count')->textInput() ?>

    <?= $form->field($model, 'forfeit_opponent_count')->textInput() ?>

    <?= $form->field($model, 'bonuspoints')->textInput() ?>

    <?= $form->field($model, 'match_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
