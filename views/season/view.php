<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Season */

$this->title = $model->name;
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

    <h3>Sessions</h3>

        <?php
          if (Yii::$app->user->can('GenericManagerPermission') && $model->status < 2) {
            echo "<p>";
            echo $model->createRegularSessionButton; 
            echo " ";
            echo $model->createPlayoffsButton; 
            echo "</p>";
          }
        ?>
<?php 
      $sessionData = new yii\data\ActiveDataProvider([
          'query' => app\models\Session::find()->where(['season_id' => $model->id])->orderBy(['date' => SORT_ASC]),
        ]);
      echo GridView::widget([
        'dataProvider' => $sessionData,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'seasonName',
            'name',
            ['attribute' => 'locationName', 'format' => 'html'],
            'typeName',
            'statusString',
            ['attribute' => 'date', 'format' => 'date',],

            ['class' => 'yii\grid\ActionColumn',
             'template' => '{session/view}',
             'buttons' => [
                'session/view' => function ($url, $match, $key) {
                    return Html::a(
                      "View",
                      $url,
                      [
                        'title' => 'View',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-success',
                      ]
                    );
                }
             ]
            ],
        ],
    ]); 
?>

    <h3>Matchpoints</h3>

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
            'Total',
        ],
      ]; 
      for ($wn = 1; $wn <= $model->regular_season_length; ++$wn) {
//        $gvdata['columns'][] = [ 'attribute' => "Week $wn group", 'label' => 'Grp', 'format' => 'html' ];
        $gvdata['columns'][] = [ 'attribute' => "Week $wn", 'label' => "Wk$wn", 'format' => 'html' ];
      }
      if (Yii::$app->user->can('GenericManagerPermission')) {
        $gvdata['columns'][] = 'id';
      }
      echo GridView::widget($gvdata);
?>

    <h3>Stats</h3>
<?php
      $gvdata2 = [
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

            ['attribute' => 'Dues Paid?', 'format' => 'html', 'label' => 'Dues'],
            'Weeks Played',
            ['attribute' => $model->playoff_qualification . ' Weeks?', 
                 'label' => $model->playoff_qualification . ' Wks?'],
            ['attribute' => 'Total', 'label' => 'Total MP'],
            ['attribute' => 'Opponent Count', 'label' => 'Opp. Ct.'],
            ['attribute' => 'Forfeit Opponent Count', 'label' => 'Forf. Opp.'],
            ['attribute' => 'Effective Opponent Count', 'label' => 'Eff. Opp.'],
            ['attribute' => 'Effective Matchpoints', 'label' => 'Eff. MP'],
//            'Lowest Wk',
//            '2nd Lowest Wk',
//            'Surplus MP',
//            'Lowest MPO',
//            'Lowest MPO float',
//            'Lowest MPO EM',
//            'Lowest MPO EO',
//            '2nd Lowest MPO',
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
      echo GridView::widget($gvdata2);
?>
    <h3>Previous Season Comparison</h3>
<?php
      $prevSeasonData = $model->previousSeasonsArrayDataProvider();
      $gridPrevSeasonData = [
        'dataProvider' => $prevSeasonData,
        'pjax' => true, 
//        'filterModel' => $searchModel,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'columns' => [
            [
              'attribute' => 'Name',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['Name'], '/player/view?id=' . $data['id']);
              },
            ],
            ['attribute' => 'Adj. MPO', 'label' => 'Adj. MPO', 'format' => ['decimal', 4]],
            ['attribute' => 'Last Season', 
             'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['Last Season'], '/season/view?id=' . $data['Last Season ID']);
              },
            ],
            ['attribute' => 'LS Adj. MPO', 'label' => 'LS Adj. MPO', 'format' => ['decimal', 4]],
            ['attribute' => 'Improvement', 'label' => 'Improvement', 'format' => ['decimal', 4]],
        ],
        'options' => [
          'style'=>'overflow: auto; word-wrap: break-word;'
        ],
      ]; 
      if (Yii::$app->user->can('GenericManagerPermission')) {
        $gridPrevSeasonData['columns'][] = 'id';
      }
      echo GridView::widget($gridPrevSeasonData);
?>

</div>
