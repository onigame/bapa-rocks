<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Match */

$this->title = $model->code;
$this->params['breadcrumbs'][] = [
   'label' => $model->season->name, 'url' => ['/season/view', 'id' => $model->season->id]
];
$this->params['breadcrumbs'][] = [
   'label' => $model->session->name, 'url' => ['/session/view', 'id' => $model->session->id]
];
$this->params['breadcrumbs'][] = $model->code;
?>
<div class="match-view">

    BROKEN

    <h1>Match <?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [ 'label' => 'Season', 'value' => $model->season->name, ],
            [ 'label' => 'Session', 'value' => $model->session->name, ],
            'code',
            'formatString',
            [ 'label' => 'Status', 'value' => $model->statusString, 'format' => 'html', ],
            'matchusersString',
        ],
    ]) ?>

</div>
