<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Game */
/* @var $form yii\widgets\ActiveForm */

/**
 * View Component: Master Selection
 * 
 * Displays the initial choice for the "Master Selector" of a game.
 * They can choose either "Machine" or "Player Order".
 * Includes PJAX for async updates and safety checks for other users.
 */
?>

<div class="game-master-selection-form">

    <h3>

<?php Pjax::begin(); ?>
<?php if ($model->masterSelector->id == Yii::$app->user->identity->id) { ?>
   You (<?= $model->masterSelector->profile->name ?>) will choose: 
    <?= Html::a('Machine', ['/game/mastermachine', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Player Order', ['/game/masterplayer', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
<?php } else { ?>
   <?= $model->masterSelector->profile->name ?> (not you) will choose: 
    <?= Html::a('Machine', ['/game/mastermachine', 'id' => $model->id], ['class' => 'btn btn-success',
       'data' => [
         'confirm' => ('You are '.Yii::$app->user->identity->profile->name
               .'. Did you get permission from '
               .$model->masterSelector->profile->name
               .' to make this selection?'),
       ],
    ]) ?>
    <?= Html::a('Player Order', ['/game/masterplayer', 'id' => $model->id], ['class' => 'btn btn-success',
       'data' => [
         'confirm' => ('You are '.Yii::$app->user->identity->profile->name
               .'. Did you get permission from '
               .$model->masterSelector->profile->name
               .' to make this selection?'),
       ],
    ]) ?>
<?php } ?>
<?php Pjax::end(); ?>

    </h3>

</div>
