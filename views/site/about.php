<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        This is the About page.
    </p>

    <p>
    <a href="/site/privacypolicy">Privacy Policy</a>
    </p>

    <p>
    <a href="/site/cookiepolicy">Cookie Policy</a>
    </p>

    <p>
    <a href="/site/tos">Terms of Service</a>
    </p>

</div>
