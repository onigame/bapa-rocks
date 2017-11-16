<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Session */

$this->title = $model->season->name . " : " . $model->name;
$this->params['breadcrumbs'][] = [ 'label' => 'Seasons', 'url' => '/season' ];
$this->params['breadcrumbs'][] = [
   'label' => $model->season->name, 'url' => ['/season/view', 'id' => $model->season->id]
];
$this->params['breadcrumbs'][] = [
   'label' => $model->name, 'url' => ['/session/view', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = "Add Late Player";
?>

    <h1>Adding Late Player</h1>

    <h2>Current Matches</h2>
<?php
    $curMatchData = new yii\data\ActiveDataProvider([
          'query' => app\models\Match::find()
                   ->where(['session_id' => $model->id, 'status' => 2])
                // ->orderBy(['code' => SORT_ASC]),
        ]);
?>
    <?= GridView::widget([
        'dataProvider' => $curMatchData,
        'responsiveWrap' => false,
        'id' => 'currentmatches',
        'pjax' => true,
        'columns' => [
 //           'id',
 //           'session_id',
            'code',
            [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
            ['attribute' => 'statusString', 'format' => 'html'],
            'matchusersScoresString',
            'formatString',
            [ 'class' => '\kartik\grid\BooleanColumn', 'label' => 'Can Add?', 
              'attribute' => 'latePlayerOkay', 'trueLabel' => 'Yes', 'falseLabel' => 'No'],
            [ 'label' => '', 'attribute' => 'admincolumn', 'format' => 'html'],
//            'statusDetailCode',
        ],
    ]); ?>

    <h2>Other Players in Season</h2>
<?= GridView::widget([
        'dataProvider' => $outdataProvider,
        'id' => 'outplayergrid',
//        'pjax' => true,
        'columns' => [
            'id',
            'Name',
            ['attribute' => 'Join', 'format' => 'html'],
        ],
    ]); ?>


</div>
