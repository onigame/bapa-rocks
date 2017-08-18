<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MachinePool */

$this->title = $model->machine_id;
$this->params['breadcrumbs'][] = ['label' => 'Machine Pools', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="machine-pool-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'machine_id' => $model->machine_id, 'user_id' => $model->user_id, 'session_id' => $model->session_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'machine_id' => $model->machine_id, 'user_id' => $model->user_id, 'session_id' => $model->session_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'pick_count',
            'machine_id',
            'user_id',
            'session_id',
        ],
    ]) ?>

</div>
