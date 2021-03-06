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

        <p>Note: If you have already signed up, you can sign in even if your email has not
         been confirmed.  Email confirmation is needed to prove you are not a spammer.</p>

        <p>Did you sign in but miss the confirmation email or window?  Request another one 
          <a class="btn btn-success" href="/user/registration/resend">here</a>.
        </p>
   
<?php      
        } else {
?>
    <h2>Playoff Date Vote!</h2>
        Please <a href="/vote">vote</a> to help us choose a playoff date.
    <h2>Your Status</h2>
        <?=  \app\models\Player::findOne(Yii::$app->user->id)->statusHtml  ?>
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
            'responsiveWrap' => false,
            'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              [ 'attribute' => 'seasonName', 'format' => 'html' ],
              'name',
              [ 'attribute' => 'locationName', 'format' => 'html'],
              'typeName',
              'statusString',
              [ 'attribute' => 'date', 'format' => 'date'],
              [ 'label' => 'Go', 'attribute' => 'GoButton', 'format' => 'html'],
            ],
          ]);
?>
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
            'responsiveWrap' => false,
            'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              [ 'attribute' => 'seasonName', 'format' => 'html'],
              'name',
              [ 'label' => 'Am I in?', 'attribute' => 'JoinButton', 'format' => 'html'],
              [ 'attribute' => 'locationName', 'format' => 'html'],
              'typeName',
              'statusString',
              [ 'attribute' => 'date', 'format' => 'date'],
              [ 'label' => 'Details', 'attribute' => 'GoButton', 'format' => 'html'],
            ],
          ]);
?>
    <h2>Past Sessions</h2>
<?php
          $sessionData = new yii\data\ActiveDataProvider([
            'query' => app\models\Session::find()->where(['status' => 2]),
            'pagination' => [ 'pageSize' => 5 ],
            'sort' => [
               'defaultOrder' => [
                  'created_at' => SORT_DESC,
               ]
            ],
          ]);
          echo GridView::widget([
            'dataProvider' => $sessionData,
            'responsiveWrap' => false,
            'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              [ 'label' => 'Season', 'attribute' => 'seasonName', 'format' => 'html'],
              'name',
              [ 'attribute' => 'locationName', 'format' => 'html'],
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
