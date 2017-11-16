<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Game */

$this->title = $model->match->code . " Game " . $model->number;
$this->params['breadcrumbs'][] = [
'label' => $model->season->name, 'url' => ['/season/view', 'id' => $model->season->id]
];
$this->params['breadcrumbs'][] = [
'label' => $model->session->name, 'url' => ['/session/view', 'id' => $model->session->id]
];
$this->params['breadcrumbs'][] = [
'label' => $model->match->code, 'url' => ['/match/view', 'id' => $model->match->id]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-view">

<?= $this->render('_game_choices', [
'model' => $model,
]) ?>

<?= DetailView::widget([
'model' => $model,
'attributes' => [
//		'match_id',
		[ 'label' => 'Machine', 'value' => $model->machineCell ],
		[ 'attribute' => 'statusString', 'format' => 'html'],
		'playerOrderSelector.name',
		'machineSelector.name',
		'masterSelector.name',
		[ 'label' => 'Players', 'value' => $model->playersString ],
],
]) ?>

</div>
