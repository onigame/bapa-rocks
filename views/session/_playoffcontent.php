<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Session */

?>

<div class="session-view">

    <h1><?= $model->season->name . " : " . $model->name ?></h1>
    <p>at <?= $model->locationName ?>, <?= Yii::$app->formatter->format($model->date, 'date') ?>

    <h2>Players</h2>
<?php
    $playoffresultsData = new yii\data\ActiveDataProvider([
          'query' => app\models\Playoffresults::find()->where(['session_id' => $model->id]),
          'sort' => [
             'attributes' => [
                'seed_max',
                'seed',
                'user.name' => [
                  'asc' => ['user.name' => SORT_ASC],
                  'desc' => ['user.name' => SORT_DESC],
                ],
                'seasonuser.mpo' => [
                  'asc' => ['seasonuser.mpo' => SORT_ASC],
                  'desc' => ['seasonuser.mpo' => SORT_DESC],
                  'label' => 'MPO',
                ],
             ],
             'defaultOrder' => [
                'seed_max' => SORT_ASC,
                'seed' => SORT_ASC,
             ],
          ],
        ]);
?>
   <?= $this->render('@app/views/playoffresults/_content', [ 'playoffresultsData' => $playoffresultsData ]) ?>

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
        'columns' => [
 //           'id',
 //           'session_id',
            'code',
            'bracket',
            'formatString',
            'matchusersString',
            ['attribute' => 'statusString', 'format' => 'html'],
//            'statusDetailCode',
            [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
        ],
    ]); ?>

    <h2>Machines at <?= $model->locationName ?></h2>
<?php
    $machineData = new yii\data\ActiveDataProvider([
          'query' => app\models\Machinerecentstatus::find()->where(['location_id' => $model->location_id]),
          'pagination' => [
            'pageSize' => 100,
          ],
          'sort' => [
             'defaultOrder' => [
                'name' => SORT_ASC,
             ]
          ],
        ]);
?>
   <?= $this->render('@app/views/machinerecentstatus/_content', [ 'machineData' => $machineData ]) ;
   ?>
    <h2>Upcoming Matches</h2>
<?php
    $curMatchData = new yii\data\ActiveDataProvider([
          'query' => app\models\Match::find()
                   ->where(['session_id' => $model->id, 'status' => 0])
                // ->orderBy(['code' => SORT_ASC]),
        ]);
?>
    <?= GridView::widget([
        'dataProvider' => $curMatchData,
        'columns' => [
 //           'id',
 //           'session_id',
            'code',
            'bracket',
            'formatString',
            'matchusersString',
            ['attribute' => 'statusString', 'format' => 'html'],
//            'statusDetailCode',
            [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
        ],
    ]); ?>

    <h2>Completed Matches</h2>
<?php
    $curMatchData = new yii\data\ActiveDataProvider([
          'query' => app\models\Match::find()
                   ->where(['session_id' => $model->id, 'status' => 3])
                // ->orderBy(['code' => SORT_ASC]),
        ]);
?>
    <?= GridView::widget([
        'dataProvider' => $curMatchData,
        'columns' => [
 //           'id',
 //           'session_id',
            'code',
            'bracket',
            'formatString',
            'matchusersString',
            ['attribute' => 'statusString', 'format' => 'html'],
//            'statusDetailCode',
            [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
        ],
    ]); ?>

</div>
