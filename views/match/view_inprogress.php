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

    <h2>Games</h2>

<?php
   $gameData = new yii\data\ActiveDataProvider([
          'query' => app\models\Game::find()->where(['match_id' => $model->id])->orderBy(['number' => SORT_ASC]),
        ]);
?>

    <?= GridView::widget([
        'dataProvider' => $gameData,
//        'options' => ['style' => 'font-size:10px'],
        'responsiveWrap' => false,
        'columns' => [
            [ 'attribute' => 'number', 'value' => function($model) { return "Game ".$model->number; } ],
            [ 'label' => 'Abbr.', 'attribute' => 'machine.abbreviation', ],
            [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
//            'match_id',
            [ 'label' => 'Machine', 'attribute' => 'MachineCell', ],
            [ 'attribute' => 'statusString', 'format' => 'html', ],
//            'statusDetailCode',
            [ 'label' => 'Winner', 'attribute' => 'WinnerName', 'format' => 'html'],
            [ 'label' => '', 'attribute' => 'admincolumn', 'format' => 'html'],
        ],
    ]); ?>

    <h2>Players</h2>
<?php
   $playerData = new yii\data\ActiveDataProvider([
          'query' => app\models\Matchuser::find()->where(['match_id' => $model->id]),
       ]);
?>
    <?= GridView::widget([
        'dataProvider' => $playerData,
//        'options' => ['style' => 'font-size:10px'],
        'responsiveWrap' => false,
        'columns' => [
            [ 'label' => 'Name', 'attribute' => 'user.name', ],
            [ 'label' => 'Matchpoints', 'attribute' => 'matchpoints', ],
            [ 'label' => 'Breakdown', 'attribute' => 'matchpointsbreakdown', ],
            [ 'label' => '', 'attribute' => 'admincolumn', 'format' => 'html'],
//            'user.id',
        ],
    ]); ?>


</div>
