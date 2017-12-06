<?php

use yii\helpers\Html;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SessionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'PvP Data';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'p1_id',
            'p2_id',
            'p1_name',
            'p2_name',

            ['class' => 'yii\grid\ActionColumn',
             'template' => '{view}',
             'buttons' => [
               'view' => function($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                          ['view', 'p1_id' => $model['p1_id'], 'p2_id' => $model['p2_id']], [
                          'title' => Yii::t('app', 'View'),]);
               }
             ]
            ],
        ],
    ]); ?>
</div>
