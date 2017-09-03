<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Location;

/* @var $this yii\web\View */
/* @var $model app\models\Machine */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="machine-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'abbreviation')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ipdb_id')->textInput()
         ->label('ID number on <a target="_blank" href="http://www.ipdb.org/search.pl">IPDB</a>')
    ?>

    <?= $form->field($model, 'location_id')->dropDownList(
         ArrayHelper::map(Location::find()->all(),'id','name'),
         ['prompt' => 'Select Location']
        )
        ->label('Location (you probably do not want to change this)')
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
