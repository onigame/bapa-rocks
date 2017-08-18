<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Session */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'typeName',
            'statusString',
            'seasonName',
            'locationName',
            'date:date',
        ],
    ]) ?>

    <h2>Matches</h2>

    <?= GridView::widget([
        'dataProvider' => $matchDataProvider,
        //'filterModel' => $matchSearchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'session_id',
            'code',
            'formatString',
            'matchusersString',
            'statusString',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn',
              'template' => '{inspect}',
              'buttons' => [
                'inspect' => function ($url) {
                  return Html::a(
                    'View',
                    $url,
                    [
                      'title' => 'View',
                      'data-pjax' => '0',
                      'class' => 'btn-sm btn-success',
                    ]
                  );
                }
              ],
            ],
        ],
    ]); ?>


</div>
