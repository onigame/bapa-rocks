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
    <?php
       if (Yii::$app->user->can('GenericManagerPermission')) {

        echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ;
        echo " ";
        echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]);
       }
    ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'id', 'visible' => Yii::$app->user->can('GenericManagerPermission')],
            'name',
            'address',
            'contact',
            'notes',
            ['attribute' => 'created_at', 'visible' => Yii::$app->user->can('GenericManagerPermission')],
            ['attribute' => 'updated_at', 'visible' => Yii::$app->user->can('GenericManagerPermission')],
        ],
    ]) ?>

    <h2>Machines</h2>
    <p>
    <?php
       if (Yii::$app->user->can('GenericManagerPermission')) {

         echo Html::a( "Add Machine",
                      ["add-machine", "location_id" => $model->id],
                      [
                        'title' => 'Add Machine',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-success',
                      ]
                    );
       }
    ?>
   
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
    <?php
       if (Yii::$app->user->can('GenericManagerPermission')) {

         echo $this->render('@app/views/machinerecentstatus/_content_admin', [ 'machineData' => $machineData ]);
       } else {
         echo $this->render('@app/views/machinerecentstatus/_content', [ 'machineData' => $machineData ]);
       }
    ?>


</div>
