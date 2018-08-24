<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PlayermachinestatsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="playermachinestats-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'machine_id') ?>

    <?= $form->field($model, 'scoremax') ?>

    <?= $form->field($model, 'scorethirdquartile') ?>

    <?php // echo $form->field($model, 'scoremedian') ?>

    <?php // echo $form->field($model, 'scorefirstquartile') ?>

    <?php // echo $form->field($model, 'scoremin') ?>

    <?php // echo $form->field($model, 'scoremaxgame_id') ?>

    <?php // echo $form->field($model, 'scoremingame_id') ?>

    <?php // echo $form->field($model, 'nonforfeitcount') ?>

    <?php // echo $form->field($model, 'totalmatchpoints') ?>

    <?php // echo $form->field($model, 'averagematchpoints') ?>

    <?php // echo $form->field($model, 'forfeitcount') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
