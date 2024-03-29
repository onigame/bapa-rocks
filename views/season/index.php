<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SeasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Seasons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Season', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'statustext',
            'name',
            [ 'label' => 'View', 'attribute' => 'ViewButton', 'format' => 'html'],
            [ 'label' => 'Playoffs', 'attribute' => 'MaybeCreatePlayoffsButton', 'format' => 'raw'],
            [ 'label' => 'Finish', 'attribute' => 'FinishSeasonButton', 'format' => 'raw'],

/*
            ['class' => 'yii\grid\ActionColumn',
               'template' => '{view} {playoff}',
               'buttons' => [
                  'playoff' => function ($url, $model, $key) {
                     return Html::a ( 
                        '<span class="glyphicon glyphicon-th-list" aria-label="Make Playoffs" aria-hidden="true" data-pjax="0"></span> ', 
                        ['season/create-playoffs', 
                         'season_id' => $model->id,
                        ],
                        ['data-pjax' => 0,
                        ]
                     );
                  },
               ],
            ],
*/
        ],
    ]); ?>
</div>
