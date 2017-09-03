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
            'user.name',
            ['attribute' => 'sessionUserInfoButton', 'label' => 'Info', 'format' => 'html'],
            ['attribute' => 'match.bracket', 'label' => 'Bracket'],
            ['attribute' => 'match.statusString', 'format' => 'html'],
            ['attribute' => 'match.code', 'header' => 'Match<br>Code'],
            ['attribute' => 'matchGoButton', 'format' => 'html'],
            ['attribute' => 'seed', 'format' => 'ordinal'],
            ['attribute' => 'seed_max', 'format' => 'ordinal', 'header' => 'Best<br>Place'],
            ['attribute' => 'true_seed_min', 'format' => 'ordinal', 'header' => 'Worst<br>Place'],
        ],
    ]); ?>


</div>
