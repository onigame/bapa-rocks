<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Playermachinestats */

$this->title = 'Create Playermachinestats';
$this->params['breadcrumbs'][] = ['label' => 'Playermachinestats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="playermachinestats-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
