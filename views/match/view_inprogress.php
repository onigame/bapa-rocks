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

<?php
     if (Yii::$app->user->can('GenericAdminPermission')) {
        echo "<p>";
        echo Html::a('Deleterecurse', ['deleterecurse', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to recursively delete this item?',
                'method' => 'post',
            ],
        ]);
        echo "</p>";
     }
?>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [ 'label' => 'Season', 'value' => $model->season->name, ],
            [ 'label' => 'Session', 'value' => $model->session->name, ],
            'code',
            'formatString',
            [ 'label' => 'Status', 'value' => $model->statusString, 'format' => 'html', ],
            'matchusersString',
            [ 'label' => 'Last Change', 'value' => $model->lastChangeTime, 'format' => ['relativetime'] ],
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
            [ 'attribute' => 'updated_at', 'format' => ['datetime', 'H:mm'] ],
            [ 'label' => '', 'attribute' => 'admincolumn', 'format' => 'html'],
        ],
    ]); ?>

    <h2>Players (by matchpoints, NOT player order of any specific game)</h2>
<?php
   $playerData = new yii\data\ActiveDataProvider([
          'query' => app\models\Matchuser::find()->where(['match_id' => $model->id])->orderBy(['matchrank' => SORT_ASC]),
       ]);
?>
    <?= GridView::widget([
        'dataProvider' => $playerData,
//        'options' => ['style' => 'font-size:10px'],
        'responsiveWrap' => false,
        'columns' => [
            [
              'attribute' => 'user.name',
              'label' => 'Name',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['user']->name, '/player/view?id=' . $data['user_id']);
              },
            ],
            [ 'label' => 'Matchpoints', 'attribute' => 'matchpoints', ],
            [ 'label' => 'Breakdown', 'attribute' => 'matchpointsbreakdown', ],
            [ 'label' => '', 'attribute' => 'admincolumn', 'format' => 'html'],
//            'user.id',
        ],
    ]); ?>


</div>
