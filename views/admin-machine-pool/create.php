<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MachinePool */

$this->title = 'Create Machine Pool';
$this->params['breadcrumbs'][] = ['label' => 'Machine Pools', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="machine-pool-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
