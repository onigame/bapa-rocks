<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $sessionuserData yii\data\ActiveDataProvider  -- showing a set of sessionusers */

?>

<div class="regularresults-view">

    <?= GridView::widget([
        'dataProvider' => $regularresultsData,
        'responsiveWrap' => false,
        'pjax' => true,
        'columns' => [
            'user.name',
//            ['attribute' => 'sessionUserInfoButton', 'label' => 'Info', 'format' => 'html'],
            ['attribute' => 'match.statusString', 'format' => 'html'],
            ['attribute' => 'match.code', 'header' => 'Match<br>Code'],
            ['attribute' => 'matchGoButton', 'format' => 'html'],
            ['attribute' => 'seasonUser.dues_string', 'format' => 'html', 'label' => 'Dues'],
        ],
    ]); ?>


</div>
