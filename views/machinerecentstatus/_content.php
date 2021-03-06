<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $sessionuserData yii\data\ActiveDataProvider  -- showing a set of sessionusers */

?>

<div class="machine-view">

    <?= GridView::widget([
        'dataProvider' => $machineData,
        'id' => 'machineview',
        'pjax' => 'true',
        'responsiveWrap' => false,
        'columns' => [
            [
              'attribute' => 'name',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['name'], '/machine/view?id=' . $data['id']);
              },
            ],

//            ['attribute' => 'ipdb_link', 'label' => 'IPDB'],
            ['attribute' => 'min', 'label' => 'Lowest', 'format' => 'decimal'],
//            ['attribute' => 'median', 'label' => 'Median', 'format' => 'decimal'],
            ['attribute' => 'max', 'label' => 'Highest', 'format' => 'decimal'],
            ['attribute' => 'string', 'label' => 'Status'],
            ['attribute' => 'machine.queueLength', 'header' => '# Groups<br>Waiting', 'format' => 'html'],
            ['attribute' => 'currentMatchInfo', 'label' => 'Current Match', 'format' => 'html'],
            [ 'label' => 'Go', 'attribute' => 'PotentialGoButton', 'format' => 'html'],
        ],
    ]); ?>

<?php
//$this->registerJs('
//    setInterval(function(){
//         $.pjax.reload({container:"#machineview-pjax", timeout:false});
//    }, 10000);', \yii\web\VIEW::POS_HEAD);
?>

</div>
