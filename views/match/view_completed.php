<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;


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

<?php
   $gameData = new yii\data\ActiveDataProvider([
          'query' => app\models\Game::find()->where(['match_id' => $model->id])->orderBy(['number' => SORT_ASC]),
        ]);
?>

    <?= GridView::widget([
        'dataProvider' => $gameData,
        'responsiveWrap' => false,
        'columns' => [
            'number',
//            'id',
//            'match_id',
            [ 'label' => 'Machine', 'attribute' => 'MachineCell', ],
            [ 'attribute' => 'statusString', 'format' => 'html' ],
//            'statusDetailCode',
            [ 'label' => 'Winner', 'attribute' => 'WinnerName', 'format' => 'html'],
            [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
        ],
    ]); ?>

   <?= Html::a("Return to Session", ['/session/view', 'id' => $model->session_id], ['class' => 'btn-sm btn-success']);
?>


</div>
