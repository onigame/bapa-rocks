<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

yii\web\JqueryAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\Season */

$this->title = 'Create Playoffs for ' . $season->name;
$this->params['breadcrumbs'][] = ['label' => 'Seasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-create">

    <h1><?= Html::encode($this->title) ?></h1>

<?php Pjax::begin(); ?>

<?= $this->render('_playoffs_form', [
   'model' => $newplayoffs
]) ?>

<?php
   $scoresData = app\models\Regularmatchpoints::seasonArrayDataProvider($season->id);
   $scoresData->sort->defaultOrder = ['IFPA Points' => SORT_ASC];
   $gvdata = [
//        'dataProvider' => $dataProvider,
        'dataProvider' => $scoresData,
        //'filterModel' => $searchModel,
        'id' => 'playergrid',
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            'Name',
//            'id',
            'su_id',
            ['class' => 'yii\grid\CheckboxColumn',
               'header' => 'A',
               'checkboxOptions' => function($model, $key, $index, $column) {
                  $value = $model['su_id'];
                  $options = ['value' => $value];
//                  $options = ['value' => $model->id];
//                  if ($model->recommended_division === 'A8') {
//                    $options['checked'] = 'true';
//                  }
                  return $options;
               }
            ],

            ['attribute' => 'IFPA Points', 'label' => 'IFPA Points'],
            ['attribute' => '5 Weeks?', 'label' => '5 Wks?'],
            ['attribute' => 'Dues Paid?', 'format' => 'html'],
            ['attribute' => 'MPO', 'label' => 'MPO', 'format' => ['decimal', 4]],
            'Weeks Played',
            'Weeks Absent',
            'Surplus MP',
            'Attendance Bonus',

        ],

    ] ;
    echo GridView::widget($gvdata);
?>
<?php Pjax::end(); ?></div>
