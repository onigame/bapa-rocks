<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;


/* @var $this yii\web\View */
/* @var $model app\models\Match */

$this->title = $model->code;
$this->params['breadcrumbs'][] = [
   'label' => $model->season->name, 'url' => ['/season/view', 'id' => $model->season->id]
];
$this->params['breadcrumbs'][] = [
   'label' => $model->session->name, 'url' => ['/session/view', 'id' => $model->session->id]
];
$this->params['breadcrumbs'][] = $model->code;
?>
<div class="match-view">

    <h1>Match <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [ 'label' => 'Season', 'value' => $model->season->name, ],
            [ 'label' => 'Session', 'value' => $model->session->name, ],
            'code',
            'formatString',
            'statusString',
            'matchusersString',
        ],
    ]) ?>

<?php
   $gameData = new yii\data\ActiveDataProvider([
          'query' => app\models\Game::find()->where(['match_id' => $model->id])->orderBy(['number' => SORT_ASC]),
        ]);
?>

    <?= GridView::widget([
        'dataProvider' => $gameData,
        'columns' => [
            'number',
//            'id',
//            'match_id',
            [ 'label' => 'Machine', 'attribute' => 'MachineCell', ],
            'statusString',
//            'statusDetailCode',
            [ 'label' => 'Winner', 'attribute' => 'WinnerName', 'format' => 'html'],

            ['class' => 'yii\grid\ActionColumn',
              'template' => '{game/go}',
              'buttons' => [
                'game/go' => function ($url, $match, $key) {
                    return Html::a(
                      "Go",
                      $url,
                      [
                        'title' => 'Go',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-success',
                      ]
                    );
                }
              ],
            ],
        ],
    ]); ?>

   <?= Html::a("Return to Session", ['/session/view', 'id' => $model->session_id], ['class' => 'btn-sm btn-success']);
?>


</div>
