<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $sessionuserData yii\data\ActiveDataProvider  -- showing a set of sessionusers */

?>

<div class="playoffresults-view">

    <?= GridView::widget([
        'dataProvider' => $playoffresultsData,
        'responsiveWrap' => false,
        'columns' => [
            [
              'attribute' => 'user.name',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['user']->name, '/player/view?id=' . $data['user_id']);
              },
            ],
            ['attribute' => 'sessionUserInfoButton', 'label' => 'Info', 'format' => 'html'],
            ['attribute' => 'match.bracket', 'label' => 'Bracket'],
            ['attribute' => 'match.statusString', 'format' => 'html'],
            ['attribute' => 'match.code', 'header' => 'Match<br>Code'],
            ['attribute' => 'matchGoButton', 'format' => 'html'],
            ['attribute' => 'seasonUser.mpo', 'label' => 'MPO', 'format' => ['decimal', 4]],
            ['attribute' => 'starting_seed', 'format' => 'ordinal', 'header' => 'Start Seed'],
            ['attribute' => 'seed', 'format' => 'ordinal', 'header' => 'Rank'],
            ['attribute' => 'seed_max', 'format' => 'ordinal', 'header' => 'Best<br>Rank'],
            ['attribute' => 'true_seed_min', 'format' => 'ordinal', 'header' => 'Worst<br>Rank'],
        ],
    ]); ?>


</div>
