<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Playermachinestats */

$this->title = $model->player->name . " on " . $model->machine->name . " [at " . $model->machine->location->name . "]";
$this->params['breadcrumbs'][] = ['label' => 'Playermachinestats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="playermachinestats-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
              'attribute' => 'playerName',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['playerName'], '/player/view?id=' . $data['user_id']);
              },
            ],
            [
              'attribute' => 'machineName',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['machineName'], '/machine/view?id=' . $data['machine_id']);
              },
            ],
            'machine_id',
            [
              'attribute' => 'scoremax',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a(Yii::$app->formatter->asDecimal($data['scoremax'], 0), '/game/view?id=' . $data['scoremaxgame_id']);
              },
            ],
            'scorethirdquartile:decimal',
            'scoremedian:decimal',
            'scorefirstquartile:decimal',
            [
              'attribute' => 'scoremin',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a(Yii::$app->formatter->asDecimal($data['scoremin'], 0), '/game/view?id=' . $data['scoremingame_id']);
              },
            ],
            'nonforfeitcount',
            'totalmatchpoints',
            'averagematchpoints',
            'forfeitcount',
        ],
    ]) ?>

</div>

<h2>Scores</h2>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $scoreDataProvider,
//        'filterModel' => $scoreSearchModel,
        'columns' => [
            //'id',
/*
            [
              'attribute' => 'Player',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['player']->name, '/player/view?id=' . $data['user_id']);
              },
            ],
*/
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



