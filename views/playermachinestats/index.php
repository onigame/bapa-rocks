<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PlayermachinestatsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Playermachinestats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="playermachinestats-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Playermachinestats', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
              'attribute' => 'playername',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['playername'], '/player/view?id=' . $data['user_id']);
              },
            ],
            [
              'attribute' => 'machinename',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['machineName'], '/machine/view?id=' . $data['machine_id']);
              },
            ],
            'scoremax',
            'scorethirdquartile',
            'scoremedian',
            'scorefirstquartile',
            'scoremin',
            // 'scoremaxgame_id',
            // 'scoremingame_id',
            'nonforfeitcount',
            // 'totalmatchpoints',
            // 'averagematchpoints',
            'forfeitcount',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
