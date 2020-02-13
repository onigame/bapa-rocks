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

<?php
     if (Yii::$app->user->can('GenericAdminPermission')) {
        echo "<p>";
        echo Html::a('Deleterecurse', ['deleterecurse', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to recursively delete this item?',
                'method' => 'post',
            ],
        ]);
        echo "</p>";
     }
?>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [ 'label' => 'Season', 'value' => $model->season->name, ],
            [ 'label' => 'Session', 'value' => $model->session->name, ],
            'code',
            'formatString',
            [ 'label' => 'Status', 'value' => $model->statusString, 'format' => 'html', ],
            'matchusersString',
        ],
    ]) ?>

    <h2>Players</h2>
<?php
   $playerData = new yii\data\ActiveDataProvider([
          'query' => app\models\Matchuser::find()->where(['match_id' => $model->id]),
       ]);
?>
    <?= GridView::widget([
        'dataProvider' => $playerData,
//        'options' => ['style' => 'font-size:10px'],
        'responsiveWrap' => false,
        'columns' => [
//            'id',
            [
              'attribute' => 'user.name',
              'label' => 'Name',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['user']->name, '/player/view?id=' . $data['user_id']);
              },
            ],

            [ 'label' => 'Matchpoints', 'attribute' => 'matchpoints', ],
            [ 'label' => 'Breakdown', 'attribute' => 'matchpointsbreakdown', ],
        ],
    ]); ?>

    <h2>Games</h2>


<?php
   $gameData = new yii\data\ActiveDataProvider([
          'query' => app\models\Game::find()->where(['match_id' => $model->id])->orderBy(['number' => SORT_ASC]),
        ]);
?>

    <?= GridView::widget([
        'dataProvider' => $gameData,
        'responsiveWrap' => false,
        'columns' => [
            'number',
//            'id',
//            'match_id',
            [ 'label' => 'Machine', 'attribute' => 'MachineCell', ],
            [ 'label' => 'Abbr.', 'attribute' => 'machine.abbreviation', ],
            [ 'attribute' => 'statusString', 'format' => 'html' ],
//            'statusDetailCode',
            [ 'label' => 'Winner', 'attribute' => 'WinnerName', 'format' => 'html'],
            [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
        ],
    ]); ?>

   <?= Html::a("Return to Session", ['/session/view', 'id' => $model->session_id], ['class' => 'btn-sm btn-success']);
?>


</div>
