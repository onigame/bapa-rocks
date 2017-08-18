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
    $sessionuserData = new yii\data\ActiveDataProvider([
          'query' => app\models\SessionUser::find()->where(['session_id' => $model->id]),
/*
          'sort' => [
             'attributes' => [
                'playoffsortcode' => [
                  'asc' => ['playoffsortcode' => SORT_ASC ],
                  'desc' => ['playoffsortcode' => SORT_DESC ],
                  'default' => SORT_ASC
                ],
             ],
             'defaultOrder' => ['currentMatch' => SORT_ASC]
          ],
*/
        ]);
?>
   <?= $this->render('@app/views/sessionuser/_content', [ 'sessionuserData' => $sessionuserData ]) ?>

    <h2>All Matches</h2>
<?php
    $matchData = new yii\data\ActiveDataProvider([
          'query' => app\models\Match::find()->where(['session_id' => $model->id])
                // ->orderBy(['code' => SORT_ASC]),
        ]);
?>

    <?= GridView::widget([
        'dataProvider' => $matchData,
        'columns' => [
 //           'id',
 //           'session_id',
            'code',
            'bracket',
            'formatString',
            'matchusersString',
            'statusString',
//            'statusDetailCode',

            ['class' => 'yii\grid\ActionColumn',
              'template' => '{match/go}',
              'buttons' => [
                'match/go' => function ($url, $match, $key) {
                    return Html::a(
                      "Go",
                      $url,
                      [
                        'title' => 'Go',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-success',
                      ]
                    );
                }
              ],
            ],
        ],
    ]); ?>


</div>
