<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model app\models\Player */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Players', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'username',
            'name',
            'initials',
            //'email:email',
            //'password_hash',
            //'auth_key',
            //'confirmed_at',
            //'unconfirmed_email:email',
            //'blocked_at',
            //'registration_ip',
            //'created_at',
            //'updated_at',
            //'flags',
            //'last_login_at',
        ],
    ]) ?>

<h2>By-Machine Stats</h2>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $statsDataProvider,
        'filterModel' => $statsSearchModel,
        'toolbar' => [
          '{export}',
          '{toggleData}',
        ],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'user_id',
            
            [
              //'attribute' => 'scoremax',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a('Details', '/playermachinestats/view?user_id=' . $data['user_id'] . '&machine_id=' . $data['machine_id']);
              },
            ],
            'machine_id',
            'locationname:text',
            [
              'attribute' => 'machinename',
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data['machineName'], '/machine/view?id=' . $data['machine_id']);
              },
            ],
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
            'nonforfeitcount:decimal',
            //'totalmatchpoints',
            'averagematchpoints:decimal',
            'forfeitcount:decimal',
            // 'created_at',
            // 'updated_at',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>



</div>
