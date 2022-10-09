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
            [ 'attribute' => 'seasonName', 'format' => 'raw'],
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
            'name',
            ['class' => 'yii\grid\ActionColumn',
              // only managers can see the remove player button.  Thank you Joel Edelman.
              'visible' => Yii::$app->user->can('GenericManagerPermission'), 
              'template' => '{removeplayer}',
              'buttons' => [
                'removeplayer' => function ($url, $mdl, $key) use ($model) {
                  return Html::a(
                    'Rmv. Plyr',
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
            ['attribute' => 'playoff_matchpoints', 'label' => 'Q.MP',
                        ],
            //'game_count',
            ['attribute' => 'opponent_count', 'label' => 'Opps'],
            //'match_count',
            'mpo',
            'previousperformance',
            ['attribute' => 'previous_season_rank', 'format'=>['decimal',2], 
                        'label'=>'P.S.Rank'],
            ['attribute' => 'x_weeks_string',
                 'label' => $model->season->playoff_qualification . ' Wks?'],
            ['attribute' => 'dues_string', 'format' => 'html'],
            ['attribute' => 'profile.vaccination', 'format'=>'vaccstatus',
                        'options'=>['style'=>'word-wrap:break-word;width:100px'],
                        'label'=>'Vacc.'],
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
        'responsiveWrap' => false,
        'id' => 'outplayergrid',
//        'pjax' => true,
        'columns' => [
            'user_id',
            'name',
//            'playerName',
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
            ['attribute' => 'playoff_matchpoints', 'label' => 'Qual. MP'],
            'game_count',
            'opponent_count',
            'match_count',
            'mpo',
            ['attribute' => 'x_weeks_string',
                 'label' => $model->season->playoff_qualification . ' Wks?'],
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
        'responsiveWrap' => false,
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
            'profile.vaccination:vaccStatus',
            ['label' => 'Toggle', 'attribute' => 'VaccToggleButton', 'format' => 'html'],
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
