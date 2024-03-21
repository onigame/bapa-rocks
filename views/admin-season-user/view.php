<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SeasonUser */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Season Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-user-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'id',
            'notes',
            'matchpoints',
            'game_count',
            'opponent_count',
            'match_count',
            'dues',
            'attendance_bonus',
            'user_id',
            'season_id',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
