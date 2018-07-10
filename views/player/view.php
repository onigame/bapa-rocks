<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Player */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Players', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'username',
            'name',
            'initials',
            //'email:email',
            //'password_hash',
            //'auth_key',
            //'confirmed_at',
            //'unconfirmed_email:email',
            //'blocked_at',
            //'registration_ip',
            //'created_at',
            //'updated_at',
            //'flags',
            //'last_login_at',
        ],
    ]) ?>

</div>
