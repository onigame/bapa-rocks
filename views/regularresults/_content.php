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
        'id' => 'regularresults',
        'pjax' => true,
        'columns' => [
            [
              'attribute' => 'user.name',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['user']->name, '/player/view?id=' . $data['user_id']);
              },
            ],
//            ['attribute' => 'sessionUserInfoButton', 'label' => 'Info', 'format' => 'html'],
            ['attribute' => 'match.statusString', 'format' => 'html'],
            ['attribute' => 'match.code', 'header' => 'Match<br>Code'],
            ['attribute' => 'matchGoButton', 'format' => 'html'],
            ['attribute' => 'seasonUser.dues_string', 'format' => 'html', 'label' => 'Dues'],
        ],
    ]); ?>

<?php
$this->registerJs('
    setInterval(function(){
         $.pjax.reload({container:"#regularresults-pjax"});
    }, 10000);', \yii\web\VIEW::POS_HEAD);
?>



</div>
