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

<div class="create-playoffs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'location_id')
       ->label("Location")
       ->dropDownList(
         ArrayHelper::map(Location::find()->all(),'id','name'),
         ['prompt' => 'Select Location']
    ) ?>

    <?= $form->field($model, 'playoff_division')
       ->label("Playoff Division")
       ->dropDownList(
         ['A' => 'A', 'B' => 'B', 'C' => 'C', 'None' => 'None', 'Other' => 'Other'],
         ['prompt' => 'Select Division']
    ) ?>


    <?= $form->field($model, 'name')->textInput([
      'maxlength' => true,
    ]) ?>

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

    <?= $form->field($model, 'playoffdata')->textInput([
      'maxlength' => true,
      'id' => 'playoffdatafield',
    ]) ?>

    <?= Html::button('Load Checked Players below into Form',
            ['class' => 'btn btn-success', 'id' => 'cpa']); ?>


    <div class="form-group">
        <?= Html::submitButton('Create Playoffs', [
          'class' => 'btn btn-success',
          'id' => 'playoffcreatebutton',
        ]) ?>
    </div>

<?=

$this->registerJs("
$(document).ready(function(){
  $('#cpa').on('click', function() {
    var keys = $('#playergrid').yiiGridView('getSelectedRows');
    var json = JSON.stringify(keys);
    $('#playoffdatafield').val( json );
  });
});
");

?>


    <?php ActiveForm::end(); ?>

</div>
