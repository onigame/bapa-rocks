<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SeasonSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="season-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'previous_season_id') ?>

    <?= $form->field($model, 'previous_season_key') ?>

    <?= $form->field($model, 'previousSeason.name') ?>

    <?= $form->field($model, 'playoff_qualification') ?>

    <?= $form->field($model, 'regular_season_length') ?>

    <?= $form->field($model, 'ifpa_weeks') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
