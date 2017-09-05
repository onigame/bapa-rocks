<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $sessionuserData yii\data\ActiveDataProvider  -- showing a set of sessionusers */

?>

<div class="machine-view">

    <?= GridView::widget([
        'dataProvider' => $machineData,
        'pjax' => true,
        'pjaxSettings' => [
          'options' => [
            'enablePushState' => false,
          ],
        ],
        'columns' => [
            'id',
            'name',
//            ['attribute' => 'ipdb_link', 'label' => 'IPDB'],
            ['attribute' => 'string', 'label' => 'Status'],
            ['attribute' => 'machine.queueLength', 'header' => '# Groups<br>Waiting', 'format' => 'html'],
            ['attribute' => 'currentMatchInfo', 'label' => 'Current Match', 'format' => 'html'],
            [ 'label' => 'Go', 'attribute' => 'PotentialGoButton', 'format' => 'html'],
            [ 'label' => 'Broken', 'attribute' => 'BrokenButton', 'format' => 'html'],
            [ 'label' => 'Gone', 'attribute' => 'GoneButton', 'format' => 'html'],
        ],
    ]); ?>


</div>
