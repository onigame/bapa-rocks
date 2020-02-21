<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Vote;
use app\models\Poll;
use app\models\PollChoice;
use app\models\SeasonUser;
/* @var $this yii\web\View */
/* @var $searchModel app\models\VoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Voting';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

<!--    Your user ID is <?= Yii::$app->user->id ?> -->

    <?php 
      $seasonusers = SeasonUser::find()
                 ->select('season_id')
                 ->joinWith('season')
                 ->where(['user_id' => Yii::$app->user->id]);
/*
      echo "Your seasons: <ul>";
      foreach ($seasonusers as $seasonuser) {
        echo "<li>";
        echo $seasonuser->season->name;
        echo "</li>";
      }
      echo "</ul>";
*/
      $polls = Poll::find()
               ->joinWith('pollEligibilities')
               ->where(['status' => 1])
               ->where(['in', 'season_id', $seasonusers])
               ->all();

      $foundone = 0;
      foreach ($polls as $poll) {
        echo "<h2>";
        echo $poll->name;
        echo "</h2>";
        echo $poll->description;
        $pcs = PollChoice::find()->where(['poll_id' => $poll->id])->all();

        foreach ($pcs as $pc) {
          $vote = Vote::find()->where(['pollchoice_id' => $pc->id, 'user_id' => Yii::$app->user->id])->one();
          if ($vote == null) {
            $vote = new Vote();
            $vote->user_id = Yii::$app->user->id;
            $vote->pollchoice_id = $pc->id;
            $vote->value = 0;
            if (!$vote->save()) {
              Yii::error($vote->errors);
              throw new \yii\base\UserException("Error creating vote");
            }
          }
          echo $this->render('@app/views/vote/_votecontent', ['model' => $vote]);
        }
       
        $foundone = 1;
      }

      if ($foundone == 0) {
        echo "(You are not eligible to vote in any open polls.)";
      }
    ?>

    <?php Pjax::end(); ?>

</div>
