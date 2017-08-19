<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Session */

$this->title = $model->season->name . " : " . $model->name;
$this->params['breadcrumbs'][] = [
   'label' => $model->season->name, 'url' => ['/season/view', 'id' => $model->season->id]
];
$this->params['breadcrumbs'][] = $model->name;
?>

<?= $this->render('@app/views/session/_content', [ 'model' => $model ]) ?>

    <?= Html::a('Finish This Session', ['finish', 'id' => $model->id], ['class' => 'btn btn-success',
       'data' => [
         'confirm' => ('Did you make sure the session should be closed?'),
       ],
    ]) ?>


</div>
