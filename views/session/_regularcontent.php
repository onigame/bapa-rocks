<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Session */

?>

<div class="session-view">

    <h1><?= $model->season->name . " : " . $model->name ?></h1>
    <p>at <?= $model->locationName ?>, <?= Yii::$app->formatter->format($model->date, 'date') ?>

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
            [ 'label' => '', 'attribute' => 'admincolumn', 'format' => 'html'],
//            'statusDetailCode',
        ],
    ]); ?>
<?php
$this->registerJs('
    setInterval(function(){
         $.pjax.reload({container:"#currentmatches-pjax"});
    }, 10000);', \yii\web\VIEW::POS_HEAD);
?>


    <h2>Players</h2>
<?php
    $regularresultsData = new yii\data\ActiveDataProvider([
          'query' => app\models\Regularresults::find()->where(['session_id' => $model->id]),
          'sort' => [
             'defaultOrder' => [
                'name' => SORT_ASC,
             ]
          ],
        ]);
?>
   <?= $this->render('@app/views/regularresults/_content', [ 'regularresultsData' => $regularresultsData ]) ?>

    <h2>Machines at <?= $model->locationName ?></h2>
<?php
;
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
   <?= $this->render('@app/views/machinerecentstatus/_content', [ 'machineData' => $machineData ]) ?>

    <h2>Finished Matches</h2>
<?php
    $finMatchData = new yii\data\ActiveDataProvider([
          'query' => app\models\Match::find()
                   ->where(['session_id' => $model->id, 'status' => 3])
                // ->orderBy(['code' => SORT_ASC]),
        ]);
?>
    <?= GridView::widget([
        'dataProvider' => $finMatchData,
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
            [ 'label' => '', 'attribute' => 'admincolumn', 'format' => 'html'],
//            'statusDetailCode',
        ],
    ]); ?>

</div>
