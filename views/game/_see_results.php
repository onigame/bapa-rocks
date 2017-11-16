<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Game */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="game-master-selection-form">

    <h3>
      Results for <?= $model->match->code ?> Game <?= $model->number ?> (<?= $model->machine->name ?>)
    </h3>

<?php
   $scoreData = new yii\data\ActiveDataProvider([
          'query' => app\models\Score::find()->where(['game_id' => $model->id])->orderBy(['playernumber' => SORT_ASC]),
        ]);

   echo kartik\grid\GridView::widget([
     'dataProvider' => $scoreData,
     'layout' => '{items}',
     'responsiveWrap' => false,
     'columns' => [
        [ 'attribute' => 'playernumber', 'label' => 'P#',
          'contentOptions' => ['style' => 'text-align:right'],
          'headerOptions' => ['style' => 'text-align:right'],
        ],
        [ 'attribute' => 'username', 'label' => 'Player Name',
          'contentOptions' => ['style' => 'text-align:left'],
          'headerOptions' => ['style' => 'text-align:left'],
        ],
        [ 'attribute' => 'scoreDisplay', 'format' => 'html', 'label' => 'Recorded Score',
          'contentOptions' => ['style' => 'text-align:right'],
          'headerOptions' => ['style' => 'text-align:right'],
        ],
        [ 'attribute' => 'matchpoints', 'format' => 'html', 'label' => 'MP',
          'contentOptions' => ['style' => 'text-align:right'],
          'headerOptions' => ['style' => 'text-align:right'],
        ],
        [ 'attribute' => 'recordername', 'format' => 'html', 'label' => 'Recorded By',
          'contentOptions' => ['style' => 'text-align:left'],
          'headerOptions' => ['style' => 'text-align:left'],
        ],
        [ 'attribute' => 'verifiername', 'format' => 'html', 'label' => 'Verified By',
          'contentOptions' => ['style' => 'text-align:left'],
          'headerOptions' => ['style' => 'text-align:left'],
        ],
        [ 'attribute' => 'id'],
     ],
   ]);

   echo Html::a("Return to Match", ['/match/view', 'id' => $model->match->id], ['class' => 'btn-sm btn-success']);
?>
  

</div>
