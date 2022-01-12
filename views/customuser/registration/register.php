<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $model
 * @var dektrium\user\Module $module
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'registration-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                ]); ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'username') ?>

                <?php if ($module->enableGeneratingPassword == false): ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                <?php endif ?>

                <label class="control-label" for="register-form-check">What P in BAPA stands for (all-lower case)</label>
                <input type="text" id="check" class="form-control" oninput="bapaCheck()">

                <script>
                  function bapaCheck() {
                    if (document.getElementById("check").value == 'pinball') {
                      document.getElementById("regSubmit").disabled = false;
                    }
                  }
                </script>

                <br>
                

                <?php if (Yii::$app->getRequest()->getUserIP() == '188.138.188.34'): ?>
                  Go Away Asshole
                <?php else: ?>
                  <?= Html::submitButton(Yii::t('user', 'Sign up'), 
                       ['class' => 'btn btn-success btn-block',
                        'id' => 'regSubmit',
                        'disabled' => 'true']) 
                  ?>
                <?php endif ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login']) ?>
        </p>
    </div>
</div>
