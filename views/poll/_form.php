<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Poll */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="poll-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pollEligibilities')->checkboxList($model->potentialEligibilities) ?>

    <?= $form->field($model, 'dates')->textInput([
      'maxlength' => true,
      'id' => 'datesfield',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
