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

<?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'id' => 'playergrid',
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            'season_id',
//            'row_number',

            'user_name',

            'recommended_division',

            ['class' => 'yii\grid\CheckboxColumn',
               // 'header' => 'A',
               'checkboxOptions' => function($model, $key, $index, $column) {
                  $options = ['value' => $model->id];
                  if ($model->recommended_division === 'A') {
                    $options['checked'] = 'true';
                  }
                  return $options;
               }
            ],

            'playoff_matchpoints',
            'matchpoints',
            'surplus_matchpoints',
//            'game_count',
//            'opponent_count',
            'mpo',
            'adjusted_mpo',
            'five_weeks_string',
            ['attribute' => 'profile.vaccination', 'format'=>'vaccstatus',
                        'label'=>'Vacc.'],
            ['attribute' => 'dues_string', 'format' => 'html'],

        ],
    ]); ?>
<?php Pjax::end(); ?></div>
