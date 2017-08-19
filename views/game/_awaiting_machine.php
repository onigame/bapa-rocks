<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\QueueGame;

/* @var $this yii\web\View */
/* @var $model app\models\Game */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="game-master-selection-form">

    <h3>
      <?= $model->machine->name ?> is currently being played by 
      <?= $model->machine->machinerecentstatus->game->playersString ?>.
<?php
  $qlen = $model->machine->queuelength;
  $posinq = $model->positionInQueue;
      if ($posinq == 1) {
        echo "(This match is next.)";
      } else {
        $nf = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
        echo "(This match is ".$nf->format($posinq)." in line.)";
      }

  $player = $model->machineSelector;
  $sameplayer = ($player->id == Yii::$app->user->identity->id);
  if ($sameplayer) {
    echo Html::a('Select Another Machine', ['/game/cancelmachine', 'id' => $model->id], ['class' => 'btn btn-success']);
  } else {
    echo Html::a('Select Another Machine', ['/game/cancelmachine', 'id' => $model->id], ['class' => 'btn btn-success',
       'data' => [
         'confirm' => ('You are '.Yii::$app->user->identity->profile->name
               .'. Did you get permission from '
               .$player->name
               .' to make this selection?'),
       ],
    ]);
  }
?>
     
    </h3>

</div>
