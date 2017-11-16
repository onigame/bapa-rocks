<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Session */

$this->title = $model->season->name . " : " . $model->name;
$this->params['breadcrumbs'][] = [ 'label' => 'Seasons', 'url' => '/season' ];
$this->params['breadcrumbs'][] = [
   'label' => $model->season->name, 'url' => ['/season/view', 'id' => $model->season->id]
];
$this->params['breadcrumbs'][] = $model->name;
?>

<?= $this->render('@app/views/session/_regularcontent', [ 'model' => $model ]) ?>

<?php if ($model->closeable) { ?>
    <?= Html::a('Finish This Session', ['finish', 'id' => $model->id], ['class' => 'btn btn-success',
       'data' => [
         'confirm' => ('Did you make sure the session should be closed?'),
       ],
    ]) ?>
<?php } ?>

<?php if ($model->lateslotcount > 0) { ?>
    <?= Html::a('Add Late Player', ['addlate', 'id' => $model->id], ['class' => 'btn btn-success',
       'data' => [
       ],
    ]) ?>
<?php } ?>


</div>
