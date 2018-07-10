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

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $statsDataProvider,
        'filterModel' => $statsSearchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'user_id',
            'machine.location.name:text:Location',
            'machine.name:text:Machine',
            [
              'attribute' => 'scoremax',
              'format' => 'raw',
              'label' => 'Best Game',
              'value' => function ($data) {
                return Html::a(Yii::$app->formatter->asDecimal($data['scoremax'], 0), '/game/view?id=' . $data['scoremaxgame_id']);
              },
            ],
            // 'scorethirdquartile',
            'scoremedian:decimal:median',
            // 'scorefirstquartile',
            [
              'attribute' => 'scoremax',
              'format' => 'raw',
              'label' => 'Worst Game',
              'value' => function ($data) {
                return Html::a(Yii::$app->formatter->asDecimal($data['scoremin'], 0), '/game/view?id=' . $data['scoremingame_id']);
              },
            ],
            'nonforfeitcount:decimal:Games',
            //'totalmatchpoints',
            'averagematchpoints:decimal:Avg. MP',
            'forfeitcount:decimal:Forfeits',
            // 'created_at',
            // 'updated_at',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>



</div>
