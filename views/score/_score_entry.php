<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Score */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
  $player = $model->user;
  $sameplayer = ($player == Yii::$app->user->identity);

  $form = ActiveForm::begin([
    'action' => 'update',
    'options' => ['class' => 'form-inline'],
  ]);

  echo $form->field($model, 'value', [
                 'options' => [
                 ],
              ])->textInput([
                'type' => 'number'
              ])->label(false);
               

  echo "blah blah";
  echo $player->name;

  ActiveForm::end();

?>
