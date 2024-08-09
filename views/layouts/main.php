<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\widgets\AlertBlock;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'BAPA Manager',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $navItems=[
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Calendar', 'url' => null,
                    'linkOptions' => ['target' => '_blank',
                                      'href' => 'https://calendar.google.com/calendar/embed?src=events%40bapa.rocks&ctz=America%2FLos_Angeles',
                                     ]],
        ['label' => 'Rules', 'url' => null,
                    'linkOptions' => ['target' => '_blank',
                                      'href' => 'https://docs.google.com/document/d/1DdaCtYqLbNSkym_TIn4lrRld6scwP5uvyGoT0ES44k0/view',
                                     ]],
        ['label' => 'Bugs', 'url' => null,
                    'linkOptions' => ['target' => '_blank', 'href' => 'https://trello.com/b/DL8y7BeG/baparocks-bug-tracker']],
        ['label' => 'Vote', 'url' => ['/vote']],
        ['label' => 'Contact', 'url' => ['/site/contact']]
      ];
      if (Yii::$app->user->isGuest) {
        array_push($navItems,['label' => 'Sign In', 'url' => ['/user/login']],['label' => 'Sign Up', 'url' => ['/user/register']]);
      } else {
        array_push($navItems,['label' => 'Settings', 'url' => ['/user/settings']]);
        array_push($navItems,['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']]
        );
      }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $navItems,
    ]);
    
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= AlertBlock::widget([ 'useSessionFlash' => true, 'type' => AlertBlock::TYPE_ALERT ]); ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; BAPA Manager <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
