<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'BAPA Manager';
?>
<div class="site-index">

    <h1>BAPA Rocks!</h1>
<?php      
        if (Yii::$app->user->isGuest) {
?>
        <p> Please Sign In or Sign Up using the buttons above.  You can either sign up using 
         a Google or Facebook account, or create an account for use on this server only.
         If you do the account on this server only, do NOT use a sensitive password that
         you use on other sites.
       </p>

        <p>Did you sign in but miss the confirmation window?  Request another one 
          <a class="btn btn-success" href="/user/registration/resend">here</a>.
        </p>
   
<?php      
        } else {
?>
    <h2>Your Status</h2>
        <?= \app\models\Player::findOne(Yii::$app->user->id)->statusHtml ?>
    <h2>Upcoming Regular Sessions</h2>
<?php
          $sessionData = new yii\data\ActiveDataProvider([
            'query' => app\models\Session::find()->where(['status' => 0, 'type' => 1]),
            'sort' => [
               'defaultOrder' => [
                  'created_at' => SORT_ASC,
               ]
            ],
          ]);
          echo GridView::widget([
            'dataProvider' => $sessionData,
            'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              'seasonName',
              'name',
              'locationName',
              'typeName',
              'statusString',
              [ 'attribute' => 'date', 'format' => 'date'],
              [ 'label' => 'Am I in?', 'attribute' => 'JoinButton', 'format' => 'html'],
              [ 'label' => 'Details', 'attribute' => 'GoButton', 'format' => 'html'],
            ],
          ]);
?>
    <h2>Current Sessions</h2>
<?php
          $sessionData = new yii\data\ActiveDataProvider([
            'query' => app\models\Session::find()->where(['status' => 1]),
            'sort' => [
               'defaultOrder' => [
                  'created_at' => SORT_ASC,
               ]
            ],
          ]);
          echo GridView::widget([
            'dataProvider' => $sessionData,
            'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              'seasonName',
              'name',
              'locationName',
              'typeName',
              'statusString',
              [ 'attribute' => 'date', 'format' => 'date'],
              [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
            ],
          ]);
?>
    <h2>Past Sessions</h2>
<?php
          $sessionData = new yii\data\ActiveDataProvider([
            'query' => app\models\Session::find()->where(['status' => 2]),
            'pagination' => [ 'pageSize' => 3 ],
            'sort' => [
               'defaultOrder' => [
                  'created_at' => SORT_ASC,
               ]
            ],
          ]);
          echo GridView::widget([
            'dataProvider' => $sessionData,
            'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              'seasonName',
              'name',
              'locationName',
              'typeName',
              'statusString',
              [ 'attribute' => 'date', 'format' => 'date'],
              [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
            ],
          ]);
?>

<?php
        };
?>

<?php
  if (Yii::$app->user->can('GenericManagerPermission')) {
?>
   <h2>Management Tools</h2>
<?php
      echo "<p>";
      echo Html::a( "Seasons",
                      ["/season"],
                      [
                        'title' => 'Seasons',
                        'data-pjax' => '0',
                        'class' => 'btn btn-success',
                      ]
                    );
      echo "</p>";
?>
<?php
      echo "<p>";
      echo Html::a( "Locations",
                      ["/location"],
                      [
                        'title' => 'Locations',
                        'data-pjax' => '0',
                        'class' => 'btn btn-success',
                      ]
                    );
      echo "</p>";
  }
?>

</div>
