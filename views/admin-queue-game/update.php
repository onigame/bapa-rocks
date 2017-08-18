<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\QueueGame */

$this->title = 'Update Queue Game: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Queue Games', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="queue-game-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
