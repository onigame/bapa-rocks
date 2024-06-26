<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Session */

$this->title = $model->season->name . " : " . $model->name;
$this->params['breadcrumbs'][] = [ 'label' => 'Seasons', 'url' => '/season' ];
$this->params['breadcrumbs'][] = [
   'label' => $model->season->name, 'url' => ['/season/view', 'id' => $model->season->id]
];
$this->params['breadcrumbs'][] = $model->name;

?>

<div class="session-view">

    <h1><?= $model->season->name . " : " . $model->name ?></h1>
    <p>at <?= $model->currentLocationName ?>, <?= Yii::$app->formatter->format($model->date, 'date') ?> [COMPLETED]

    <h2>Matches</h2>
<?php
    $curMatchData = new yii\data\ActiveDataProvider([
          'query' => app\models\Match::find()
                   ->where(['session_id' => $model->id])
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
//            'matchusersString',
//            ['attribute' => 'statusString', 'format' => 'html'],
            'matchusersScoresString',
            'formatString',
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

    <h2>Machines at <?= $model->currentLocationName ?></h2>
<?php
    $machineData = new yii\data\ActiveDataProvider([
          'query' => app\models\Machinerecentstatus::find()->where(['location_id' => $model->currentLocation->id]),
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

</div>
