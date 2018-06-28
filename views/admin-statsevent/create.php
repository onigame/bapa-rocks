<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Statsevent */

$this->title = 'Create Statsevent';
$this->params['breadcrumbs'][] = ['label' => 'Statsevents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statsevent-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
