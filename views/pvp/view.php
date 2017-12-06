<?php

use yii\helpers\Html;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SessionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $p1_name . " vs. " . $p2_name;
$this->params['breadcrumbs'][] = [
   'label' => 'PvP Data', 'url' => ['/pvp/index']
];
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

            //'p1_id',
            //'p2_id',
            'season_name',
            'session_name',
            'match_code',
            'game_number',
            'machine_name',
            'p1_score',
            'p2_score',
            'winner_name',

        ],
    ]); ?>
</div>
