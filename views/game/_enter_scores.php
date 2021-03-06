<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\data\ActiveDataProvider;
use app\models\Score;


/* @var $this yii\web\View */
/* @var $model app\models\Game */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="game-master-selection-form">

<?php
   $headerline = "<h3> Enter Scores for ";
   $headerline .= $model->match->code;
   $headerline .= " Game ";
   $headerline .= $model->number;
   $headerline .= " (";
   $headerline .= $model->machine->name;
   $headerline .= ") ";
   $headerline .= $model->finishButton;
   $headerline .= "</h3>";

   $scoreData = new yii\data\ActiveDataProvider([
          'query' => app\models\Score::find()->where(['game_id' => $model->id])->orderBy(['playernumber' => SORT_ASC]),
        ]);

   $javascript_hack = <<<JAVASCRIPT
<script type="text/javascript">
$(document).ready(function() {
  for (lcv=0; lcv<10; lcv++) {
    popover_id = "#score-"+lcv+"-value-popover";
    $(popover_id).on('shown.bs.modal', function(event) {
      display_id = "#"+event.target.id.replace(/popover$/,'disp');
      $(display_id).select();
    });
  }
  // Make it so that all the submit buttons auto-submit when focus is gained.
  $('.kv-editable-submit').each(function(i, obj) {
    $(this).on('focus', function (event) {
      $(this).click();
    });
  });
});
</script>
JAVASCRIPT;

   echo kartik\grid\GridView::widget([
     'dataProvider' => $scoreData,
     'layout' => '{items}',
     'responsiveWrap' => false,
     'pjax' => true,
     'pjaxSettings' => [
          'beforeGrid' => $headerline,
          'afterGrid' => $javascript_hack,
        ],
     'columns' => [
        [ 'attribute' => 'playernumber', 'label' => 'P#',
          'contentOptions' => ['style' => 'text-align:right'],
          'headerOptions' => ['style' => 'text-align:right'],
        ],
        [ 'class' => 'kartik\grid\EditableColumn',
          'attribute' => 'value',
          'label' => 'Change Score',
          'format' => ['decimal', 0],
          'refreshGrid' => true,
          'contentOptions' => ['style' => 'text-align:right'],
          'headerOptions' => ['style' => 'text-align:right'],
          'editableOptions' => [
//            'pjaxContainerId' => $pjax->id,
            'inputType' => 'widget',
            'widgetClass' => '\extead\autonumeric\AutoNumeric',
            'buttonsTemplate' => '{submit}{reset}',
            'options' => [
              'class' => '\extead\autonumeric\AutoNumeric',
              'pluginOptions' => [
                'allowClear' => true,
                'decimalPlaces' => '0',
                'aSep' => ',',
                'vMin' => '0',
                'vMax' => '9999999999999',
              ],
            ],
          ],
          'readonly' => function ($model, $key, $index, $widget) {
            return ($model->forfeit == 1);
          },
        ],
        [ 'attribute' => 'username', 'label' => 'Player Name',
          'contentOptions' => ['style' => 'text-align:left'],
          'headerOptions' => ['style' => 'text-align:left'],
          'format' => 'raw',
          'value' => function ($data) {
             return Html::a($data['username'], '/playermachinestats/view?user_id=' . $data['user_id'] . '&machine_id=' . $data['game']->machine_id);
          },
        ],
        [ 'attribute' => 'scoreDisplay', 'format' => 'html', 'label' => 'Recorded Score',
          'contentOptions' => ['style' => 'text-align:right'],
          'headerOptions' => ['style' => 'text-align:right'],
        ],
        [ 'attribute' => 'verifycolumn',
          'label' => 'Verify?',
          'format' => 'raw',
        ],
        [ 'attribute' => 'recordername', 'format' => 'html', 'label' => 'Recorded By',
          'contentOptions' => ['style' => 'text-align:left'],
          'headerOptions' => ['style' => 'text-align:left'],
        ],
        [ 'attribute' => 'verifiername', 'format' => 'html', 'label' => 'Verified By',
          'contentOptions' => ['style' => 'text-align:left'],
          'headerOptions' => ['style' => 'text-align:left'],
        ],
        [ 'label' => 'Forfeit?', 'attribute' => 'ForfeitButton', 'format' => 'raw'],
        [ 'label' => '', 'attribute' => 'admincolumn', 'format' => 'html'],
     ],
   ]);
   $this->registerAssetBundle(yii\web\JqueryAsset::className(), \yii\web\View::POS_HEAD);
?>

<?php
          if (Yii::$app->user->can('GenericManagerPermission')) {
            //echo "<h3>Manager Actions</h3>";
            echo "<p>";
            echo Html::a('Kick Group Off Machine', ['kick', 'id' => $model->id], [
                  'class' => 'btn btn-warning',
                  'data' => [
                    'confirm' => ('Did you make sure this is an intentional kick?'),
                  ],
                 ]);
            echo "</p>";
          }

?>

</div>
