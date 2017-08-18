<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MachinePool */

$this->title = 'Update Machine Pool: ' . $model->machine_id;
$this->params['breadcrumbs'][] = ['label' => 'Machine Pools', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->machine_id, 'url' => ['view', 'machine_id' => $model->machine_id, 'user_id' => $model->user_id, 'session_id' => $model->session_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="machine-pool-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
