<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SeasonUser */

$this->title = 'Create Season User';
$this->params['breadcrumbs'][] = ['label' => 'Season Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
