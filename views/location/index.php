<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Locations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
    <?php
       if (Yii::$app->user->can('GenericManagerPermission')) {
          echo Html::a('Create Location', ['create'], ['class' => 'btn btn-success']); 
       }
    ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [ 'label' => 'View', 'attribute' => 'ViewButton', 'format' => 'html'],
            'name',
            'address',
            'contact',
            'notes',
            'id',
            // 'created_at',
            // 'updated_at',

            [
               'class' => 'yii\grid\ActionColumn',
               'visibleButtons' => [
                 'update' => Yii::$app->user->can('GenericManagerPermission'),
                 'delete' => Yii::$app->user->can('GenericManagerPermission'),
               ],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
