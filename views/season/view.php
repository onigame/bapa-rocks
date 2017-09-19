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
          if (Yii::$app->user->can('GenericManagerPermission')) {
            echo "<p>";
            echo Html::a('Create Regular Session', ['create-session', 'season_id' => $model->id], ['class' => 'btn btn-success']);
            echo " ";
            echo Html::a('Create Playoffs', ['create-playoffs', 'season_id' => $model->id], ['class' => 'btn btn-success']);
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
            'locationName',
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

    <h3>Scores</h3>

(TODO: make this a grid with weeks as columns)

<?php 
      $scoresData = new yii\data\ActiveDataProvider([
          'query' => app\models\Regularmatchpoints::find()->where(['season_id' => $model->id])
        ]);
      echo GridView::widget([
        'dataProvider' => $scoresData,
//        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'name',
            ['attribute' => 'session_name', 'label' => 'Week',],
            ['attribute' => 'code', 'label' => 'Group',],
            'matchpoints',

        ],
    ]); 
?>

</div>
