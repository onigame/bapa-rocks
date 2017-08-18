<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Season */

$this->title = 'Create Season';
$this->params['breadcrumbs'][] = ['label' => 'Seasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
