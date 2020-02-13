<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PollEligibility */

$this->title = 'Create Poll Eligibility';
$this->params['breadcrumbs'][] = ['label' => 'Poll Eligibilities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="poll-eligibility-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
