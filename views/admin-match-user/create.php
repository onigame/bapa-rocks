<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MatchUser */

$this->title = 'Create Match User';
$this->params['breadcrumbs'][] = ['label' => 'Match Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="match-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
