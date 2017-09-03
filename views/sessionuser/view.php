<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\SessionUser */

$this->title = $model->user->name . " @ " . $model->session->name;
$this->params['breadcrumbs'][] = ['label' => $model->session->season->name, 'url' => ['/season/view', 'id' => $model->session->season_id]];
$this->params['breadcrumbs'][] = ['label' => $model->session->name, 'url' => ['/session/view', 'id' => $model->session_id]];
$this->params['breadcrumbs'][] = $model->user->name;
?>
<div class="session-user-view">

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
            //'status',
            //'user_id',
            'user.name',
            //'session_id',
            'session.name',
            //'recorder_id',
            'recorder.name',
            //'created_at',
            //'updated_at',
            'tiebreaker',
            'selectionThreshold',
        ],
    ]) ?>

    <h3>Past Picks</h3>
 
<?php
   $provider = new yii\data\ActiveDataProvider([
          'query' => app\models\MachinePool::find()->where(['user_id' => $model->user_id, 'session_id' => $model->session_id])
        ]);

   echo \kartik\grid\GridView::widget([
     'dataProvider' => $provider,
     'columns' => [
       'machine.name',
       'pick_count',
     ]
   ]);
?>

    <h3>Selectable Machines</h3>

<?php
    $data = $model->selectableMachineList;
    echo "<UL>";
    foreach ($data as $id => $machinetext) {
      echo "<LI>";
      echo $machinetext;
    }
    echo "</UL>";
?>

</div>
