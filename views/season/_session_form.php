<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\datecontrol\DateControl;
use app\models\Location;

/* @var $this yii\web\View */
/* @var $model app\models\Session */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="session-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'location_id')
       ->label("Location")
       ->dropDownList(
         ArrayHelper::map(Location::find()->all(),'id','name'),
         ['prompt' => 'Select Location']
    ) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->widget(DateControl::className(), [
           'type'=>DateControl::FORMAT_DATE,
           'widgetOptions' => [
             'pluginOptions' => [
               'autoclose' => true,
               'startDate' => "0d",
             ]
           ]
         ])
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
