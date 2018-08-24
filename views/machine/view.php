<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Machine */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Machines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="machine-view">

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
            'name',
            'abbreviation',
            [
              'attribute' => 'ipdb_id',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['ipdb_id'], 
                   "http://www.ipdb.org/machine.cgi?id=" . $data['ipdb_id'],
                   [
                     "target" => "_blank",
                   ]
                );
              },
            ],

            'location_id',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>

<h2>Scores</h2>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $scoreDataProvider,
//        'filterModel' => $scoreSearchModel,
        'columns' => [
            //'id',
            [
              'attribute' => 'playerName',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['player']->name, '/player/view?id=' . $data['user_id']);
              },
            ],
            'playernumber',
            [
              'attribute' => 'Score',
              'format' => 'decimal',
              'value' => 'value',
            ],
            'matchpoints',
            'forfeit',
//            'verified',
//            'nonforfeitcount',
            [
              'attribute' => 'Season',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['game']->match->session->season->name, '/season/view?id=' . $data['game']->match->session->season->id);
              },
            ],
            [
              'attribute' => 'Session',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['game']->match->session->name, '/session/view?id=' . $data['game']->match->session->id);
              },
            ],
            [
              'attribute' => 'Match',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['game']->match->code, '/match/view?id=' . $data['game']->match->id);
              },
            ],
            [
              'attribute' => 'Game',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a('Game '.$data['game']->number, '/game/view?id=' . $data['game_id']);
              },
            ],
//            'recorder_id',
//            'verifier_id',
//            'created_at',
//            'updated_at',
        ],
    ]); ?>
<?php Pjax::end(); ?></div>

