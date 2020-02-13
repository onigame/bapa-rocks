<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PollChoice */

$this->title = 'Create Poll Choice';
$this->params['breadcrumbs'][] = ['label' => 'Poll Choices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="poll-choice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
