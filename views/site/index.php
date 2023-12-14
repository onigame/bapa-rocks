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
        <p> Please 
         <a class="btn btn-success" href="/user/login">Sign In</a>
         or 
         <a class="btn btn-success" href="/user/register">Sign Up</a>
         You can either sign up using 
         a Google or Facebook account, or create an account for use on this server only.
         If you do the account on this server only, do NOT use a sensitive password that
         you use on other sites.
       </p>

        <p>Note: If you have already signed up, you can sign in even if your email has not
         been confirmed.  Email confirmation is needed to prove you are not a spammer.</p>

        <p>Did you sign in but miss the confirmation email or window?  Request another one 
          <a class="btn btn-success" href="/user/registration/resend">here</a>.
        </p>
   
   <h2>What is this?</h2>

      <p> This is the website that manages scoring for the Bay Area Pinball Association. 
         We have a pinball league that runs four seasons a year, with a big playoff party
         to cap off the year.  Each season is 10 weeks, meeting Thursday evenings at a
         location in the south San Francisco Bay Area.

      <p> To attend the playoff party, you must play at least 4 of the 10 weeks and pay
         membership dues (currently $15).

      <p> The 10 weeks of season play is <a href="https://www.ifpapinball.com/">IFPA</a> 
         sanctioned. To be eligible for IFPA points, you must play at least 5 of the 
         10 weeks.

      <p> For more information and details, please join <a href="https://www.facebook.com/groups/bapamembers">our Facebook group</a>.
         You may also read our <a href="https://docs.google.com/document/d/1DdaCtYqLbNSkym_TIn4lrRld6scwP5uvyGoT0ES44k0/view">League Rules</a>
         or send an email to onigame at sign gmail dot com.
<?php      
        } else {
?>
<!--
    <h2>Playoff Date Vote!</h2>
        If you are eligible, please <a class="btn btn-success" href="/vote">vote</a> to help us choose a playoff date.
-->

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
