<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MachineStatus */

$this->title = 'Update Machine Status: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Machine Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="machine-status-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
