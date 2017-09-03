<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Machine */

$this->title = 'Add Machine';
$this->params['breadcrumbs'][] = [
   'label' => 'Locations', 'url' => ['/location']
];
$this->params['breadcrumbs'][] = [
   'label' => $model->location->name, 'url' => ['/location/view', 'id' => $model->location_id]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="machine-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_machine_form', [
        'model' => $model,
    ]) ?>

</div>
