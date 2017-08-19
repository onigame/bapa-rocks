<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Location */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-view">

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
            'address',
            'contact',
            'notes',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <h2>Machines</h2>
<?php
    $machineData = new yii\data\ActiveDataProvider([
          'query' => app\models\Machinerecentstatus::find()->where(['location_id' => $model->id]),
          'sort' => [
             'defaultOrder' => [
                'name' => SORT_ASC,
             ]
          ],
          'pagination' => [
            'pageSize' => 100,
          ],
        ]);
?>
  <?= $this->render('@app/views/machinerecentstatus/_content_admin', [ 'machineData' => $machineData ]) ?>


</div>
