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

<?php
  if (Yii::$app->user->can('GenericAdminPermission')) {
        echo "<h2>Admin Tools</h2>";
        echo "<p>";
        echo Html::a('Deleterecurse', ['deleterecurse', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to recursively delete this item?',
                'method' => 'post',
            ],
        ]);
        echo "</p>";
  }
?>

<?php
  if (Yii::$app->user->can('GenericManagerPermission')) {
?>
   <h2>Management Tools</h2>

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

<?php } ?>

</div>
