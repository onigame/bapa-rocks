<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Season */

$this->title = $model->name . ' and ' . $model2->name;
$this->params['breadcrumbs'][] = ['label' => 'Seasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
/*
          if (Yii::$app->user->can('GenericManagerPermission')) 
            echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
          echo " ";
          if (Yii::$app->user->can('nonexistentpermission')) 
            echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
          ]);
*/
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
//            'status',
            'statustext',
            'name',
            'previousSeason.name',
//            'created_at',
//            'updated_at',
        ],
    ]) ?>

    <?= DetailView::widget([
        'model' => $model2,
        'attributes' => [
            'id',
//            'status',
            'statustext',
            'name',
            'previousSeason.name',
//            'created_at',
//            'updated_at',
        ],
    ]) ?>

    <h3>Stats</h3>
<?php
      $scoresData = app\models\Regularmatchpoints::seasonArrayDataProvider($model->id);
      $gvdata = [
        'dataProvider' => $scoresData,
        'pjax' => true, 
//        'filterModel' => $searchModel,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            [
              'attribute' => 'Name',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['Name'], '/player/view?id=' . $data['id']);
              },
            ],
//            ['attribute' => 'session_name', 'label' => 'Week',],
//            ['attribute' => 'code', 'label' => 'Group',],
//            'matchpoints',
            'Playoff Qual. Score',
            ['attribute' => 'MPO', 'label' => 'MPO', 'format' => ['decimal', 4]],

            ['attribute' => 'Total', 'label' => 'Total MP'],
            ['attribute' => 'Opponent Count', 'label' => 'Opp. Ct.'],
            ['attribute' => 'Forfeit Opponent Count', 'label' => 'Forf. Opp.'],
            ['attribute' => 'Effective Opponent Count', 'label' => 'Eff. Opp.'],
            ['attribute' => 'Effective Matchpoints', 'label' => 'Eff. MP'],
            'Lowest Wk',
            '2nd Lowest Wk',
//            'Surplus MP',
            'Lowest MPO',
//            'Lowest MPO float',
//            'Lowest MPO EM',
//            'Lowest MPO EO',
            '2nd Lowest MPO',
//            '2nd Lowest MPO float',
//            '2nd Lowest MPO EM',
//            '2nd Lowest MPO EO',
            ['attribute' => 'Adj. MPO', 'label' => 'Adj. MPO', 'format' => ['decimal', 4]],
        ],
        'options' => [
          'style'=>'overflow: auto; word-wrap: break-word;'
        ],
      ]; 
//      for ($wn = 1; $wn <= 12; ++$wn) {
//        $gvdata['columns'][] = [ 'attribute' => "Week $wn group", 'label' => 'Grp', 'format' => 'html' ];
//        $gvdata2['columns'][] = [ 'attribute' => "Week $wn", 'label' => "Wk$wn", 'format' => 'html' ];
//      }
      if (Yii::$app->user->can('GenericManagerPermission')) {
        $gvdata2['columns'][] = 'id';
      }
      echo GridView::widget($gvdata);
?>

</div>
