<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PollEligibility */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="poll-eligibility-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'season_id')->textInput() ?>

    <?= $form->field($model, 'poll_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
