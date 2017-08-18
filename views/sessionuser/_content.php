<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $sessionuserData yii\data\ActiveDataProvider  -- showing a set of sessionusers */

?>

<div class="sessionuser-view">

    <?= GridView::widget([
        'dataProvider' => $sessionuserData,
        'columns' => [
            'user.name',
            'seasonMatchpoints',
            'seasonmpg',
            ['attribute' => 'currentMatchBracket', 'label' => 'Bracket'],
            'currentMatchString',
            'currentMatchStatus',
            ['attribute' => 'currentMatchAction', 'format' => 'html'],
            ['attribute' => 'currentSeed', 'format' => 'ordinal'],
            ['attribute' => 'best', 'format' => 'ordinal'],
            ['attribute' => 'worst', 'format' => 'ordinal'],
 //           'id',
 //           'session_id',
/*
            'code',
            'bracket',
            'formatString',
            'matchusersString',
            'statusString',
*/
//            'statusDetailCode',
/*
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
*/
        ],
    ]); ?>


</div>
