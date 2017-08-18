<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Machine */

$this->title = 'Create Machine';
$this->params['breadcrumbs'][] = ['label' => 'Machines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="machine-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
