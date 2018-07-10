<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Playermachinestats */

$this->title = 'Update Playermachinestats: ' . $model->user_id;
$this->params['breadcrumbs'][] = ['label' => 'Playermachinestats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['view', 'user_id' => $model->user_id, 'machine_id' => $model->machine_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="playermachinestats-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
