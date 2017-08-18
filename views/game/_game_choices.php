<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Game */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="game-choices">

<?php if ($model->status == 0) { ?>
    <?= $this->render('_master_selection', [
        'model' => $model,
    ]) ?>
<?php } else if ($model->status == 1) { ?>
    <?= $this->render('_other_selection', [
        'model' => $model,
    ]) ?>
<?php } else if ($model->status == 2) { ?>
    <?= $this->render('_awaiting_machine', [
        'model' => $model,
    ]) ?>
<?php } else if ($model->status == 3) { ?>
    <?= $this->render('_enter_scores', [
        'model' => $model,
    ]) ?>
<?php } else if ($model->status == 4) { ?>
    <?= $this->render('_see_results', [
        'model' => $model,
    ]) ?>
<?php } ?>

</div>
