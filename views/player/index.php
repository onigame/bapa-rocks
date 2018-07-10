<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PlayerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Players';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Player', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            //'username',
            'name',
            'initials',
            // 'email:email',
            //'password_hash',
            // 'auth_key',
            // 'confirmed_at',
            // 'unconfirmed_email:email',
            // 'blocked_at',
            // 'registration_ip',
            // 'created_at',
            // 'updated_at',
            // 'flags',
            // 'last_login_at',

            ['class' => 'yii\grid\ActionColumn',
             'template' => '{view}',
             'buttons' => [
               'view' => function($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                          ['view', 'id' => $model['id']], [
                          'title' => Yii::t('app', 'View'),]);
               }
             ]
            ],

        ],
    ]); ?>
<?php Pjax::end(); ?></div>
