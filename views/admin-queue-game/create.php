<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\QueueGame */

$this->title = 'Create Queue Game';
$this->params['breadcrumbs'][] = ['label' => 'Queue Games', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="queue-game-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
