<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Playermachinestats */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="playermachinestats-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'machine_id')->textInput() ?>

    <?= $form->field($model, 'scoremax')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'scorethirdquartile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'scoremedian')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'scorefirstquartile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'scoremin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'scoremaxgame_id')->textInput() ?>

    <?= $form->field($model, 'scoremingame_id')->textInput() ?>

    <?= $form->field($model, 'nonforfeitcount')->textInput() ?>

    <?= $form->field($model, 'totalmatchpoints')->textInput() ?>

    <?= $form->field($model, 'averagematchpoints')->textInput() ?>

    <?= $form->field($model, 'forfeitcount')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
