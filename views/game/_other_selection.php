<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SessionUser;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Game */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="game-other-selection-form">

<?php Pjax::begin(); ?>
    <h3>
<?php if ($model->scores != null) { ?>
    <!-- no player order selection, order already done. -->
<?php } else if ($model->playerOrderSelector->id == Yii::$app->user->identity->id) { ?>
   You (<?= $model->playerOrderSelector->name ?>) will choose to be:
    <?= Html::a('Player 1', ['/game/playerorder', 'id' => $model->id, 'order' => 1], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Player 2', ['/game/playerorder', 'id' => $model->id, 'order' => 2], ['class' => 'btn btn-success']) ?>
<?php } else { ?>
  <?= $model->playerOrderSelector->name ?> (not you) will choose to be:
    <?= Html::a('Player 1', ['/game/playerorder', 'id' => $model->id, 'order' => 1], ['class' => 'btn btn-success',
       'data' => [
         'confirm' => ('You are '.Yii::$app->user->identity->profile->name
               .'. Did you get permission from '
               .$model->playerOrderSelector->profile->name
               .' to make this selection?'),
       ],
    ]) ?>
    <?= Html::a('Player 2', ['/game/playerorder', 'id' => $model->id, 'order' => 2], ['class' => 'btn btn-success',
       'data' => [
         'confirm' => ('You are '.Yii::$app->user->identity->profile->name
               .'. Did you get permission from '
               .$model->playerOrderSelector->profile->name
               .' to make this selection?'),
       ],
    ]) ?>
<?php } ?>
    </h3>

<?php 
      $player = $model->machineSelector;
      if ($model->machine != null) { ?>
    <!-- no player machine selection, already done. -->
<?php } else if ($model->master_selector == null) { ?>
    <!-- no player machine selection, auto-selector needs to do it. -->
<?php 
  } else {
    $sameplayer = ($player->id == Yii::$app->user->identity->id);
    $sessionuser = SessionUser::findOne(['user_id' => $player->id, 'session_id' => $model->session->id]);
    $form = ActiveForm::begin([
      'method' => 'get',
      'action' => 'selectmachine',
      'options' => ['class' => 'form-inline'],
    ]);
    echo Html::activeHiddenInput($model, 'id', ['name'=>'id']);
    echo "<h3>";
    echo ($sameplayer ? "You ($player->name)" : $player->name." (not you)");
    echo " should choose a machine:";
    echo $form->field($model, 'machine_id')->dropDownList(
            $sessionuser->selectableMachineList, 
            ['prompt' => ['text' => 'choose a machine...', 
                          'options' => ['value' => '-1',
                         ]],
             'name'=>'machine_id'])->label(false);
    if ($sameplayer) {
      echo Html::submitButton('Select This Machine', ['class' => 'btn btn-success']);
    } else {
      echo Html::submitButton('Select This Machine', ['class' => 'btn btn-success',
         'data-confirm' => ('You are '.Yii::$app->user->identity->profile->name
               .'. Did you get permission from '
               .$player->name
               .' to make this selection?'),
      ]);
    }
    echo "</h3>";
    ActiveForm::end();
  } 
?>
<?php Pjax::end(); ?>

</div>
