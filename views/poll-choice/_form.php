<?php

use app\models\Poll;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PollChoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="poll-choice-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(
             ['0' => '0: Visible', '1' => '1: Hidden']
                                       ) ?>

    <?= $form->field($model, 'poll_id')->dropDownList(
                         ArrayHelper::map(Poll::find()->all(), 'id', 'name'),
                         ['prompt'=>'Select Option']
                                         ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
