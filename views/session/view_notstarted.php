<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\widgets\Pjax;

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

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'typeName',
//            'statusString',
            'seasonName',
            [
              'attribute' => 'locationName',
              'format' => 'raw',
              'value' => Html::a($model->locationName, ['/location/view', 'id' => $model->location_id]),
            ],
            'date:date',
        ],
    ]) ?>

    <p>This event has not started yet; players can still be added/removed.</p>

    <?=
      Html::a('Start ' . $model->name,
        [ 'start',
          'id' => $model->id,
        ],
        [
          'class' => 'btn-lg btn-success',
        ]
      );
    ?>

    <h2>Included Players</h2>

<?= GridView::widget([
        'dataProvider' => $indataProvider,
        'id' => 'inplayergrid',
        'responsiveWrap' => false,
//        'pjax' => true,
        'columns' => [
            'user_id',
            'playerName',
            ['class' => 'yii\grid\ActionColumn',
              'template' => '{removeplayer}',
              'buttons' => [
                'removeplayer' => function ($url, $mdl, $key) use ($model) {
                  return Html::a(
                    'Remove Player',
                    [ 'removeplayer',
                      'session_id' => $model->id,
                      'seasonuser_id' => $key,
                    ],
                    [
                      'title' => 'View',
                      'class' => 'btn-sm btn-danger',
                    ]
                  );
                }
              ],
            ],
            //'notes',
            'matchpoints',
            //'game_count',
            'opponent_count',
            'match_count',
            'mpo',
            'previousperformance',
            'previous_season_rank',
            'five_weeks_string',
            ['attribute' => 'dues_string', 'format' => 'html'],
        ],
    ]); ?>
<?php 
/*
$this->registerJs(' 
    setInterval(function(){  
         $.pjax.reload({container:"#inplayergrid"});
    }, 10000);', \yii\web\VIEW::POS_HEAD); 
*/
?>

    <h2>Other Players in Season</h2>
<?= GridView::widget([
        'dataProvider' => $outdataProvider,
        'id' => 'outplayergrid',
//        'pjax' => true,
        'columns' => [
            'user_id',
            'playerName',
            ['class' => 'yii\grid\ActionColumn',
              'template' => '{addplayer}',
              'buttons' => [
                'addplayer' => function ($url, $mdl, $key) use ($model) {
                  return Html::a(
                    'Add Player',
                    [ 'addplayer',
                      'session_id' => $model->id,
                      'seasonuser_id' => $key,
                    ],
                    [
                      'title' => 'View',
                      'class' => 'btn-sm btn-success',
                    ]
                  );
                }
              ],
            ],
            'notes',
            'matchpoints',
            'game_count',
            'opponent_count',
            'match_count',
            'mpo',
            'five_weeks_string',
            ['attribute' => 'dues_string', 'format' => 'html'],
        ],
    ]); ?>
<?php
/*
$this->registerJs(' 
    setInterval(function(){  
         $.pjax.reload({container:"#outplayergrid"});
    }, 10000);', \yii\web\VIEW::POS_HEAD); 
*/
?>

<?php
  if (Yii::$app->user->can('GenericManagerPermission')) {
?>
    <h2>All Players (Managers only)</h2>

<?= GridView::widget([
        'dataProvider' => $otherdataProvider,
        'id' => 'playergrid',
        'columns' => [
            'id',
            'name',
            'profile.ifpa',
            ['class' => 'yii\grid\ActionColumn',
              'template' => '{addplayer}',
              'buttons' => [
                'addplayer' => function ($url, $mdl, $key) use ($model) {
                  return Html::a(
                    'Add Player',
                    [ 'addotherplayer',
                      'session_id' => $model->id,
                      'user_id' => $key,
                    ],
                    [
                      'title' => 'View',
                      'class' => 'btn-sm btn-success',
                    ]
                  );
                }
              ],
            ],
            'profile.user.username',
/*
            'notes',
            'matchpoints',
            'game_count',
            'opponent_count',
            'match_count',
            'mpg',
            'five_weeks_string',
            'dues_string',
*/
        ],
    ]); ?>

<?php
  }
?>


</div>
