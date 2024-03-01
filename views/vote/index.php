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
               ->where(['in', 'season_id', $seasonusers])
               ->where(['status' => 1])
               ->all();

      $foundone = 0;
      foreach ($polls as $poll) {
        echo "<h2>";
        echo $poll->name;
        echo "</h2>";
        echo $poll->description;
        $pcs = PollChoice::find()->where(['poll_id' => $poll->id, 'status' => 0])->all();

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

    <h2>General Rules</h2>

    <ol>

       <li>Specific polls may override these rules as necessary.

       <li>You may change your vote at any time until the poll closes.

       <li>"Might Not Show Up" is the default value. A vote for anything else is a committment to come on that date if it wins.

       <li>The winning date is the date with the fewest "Might Not Show Up" votes.

       <li>In case of a tie, the winning date is the date with the most points.
 
       <ul>

       <li>How do we compute points? Well, we start out by figuring out your PITA, which is the proportion of dates that you said "Might Not Show Up" to. For example:
          <ul>
          <li>If you said "Might Not Show Up" to half of the dates, your PITA is 0.5.  
          <li>If you said "Might Not Show Up" to a quarter of the dates, your PITA is 0.25.  
          <li>If you didn't say "Might Not Show Up" at all, your PITA is 0.
          </ul>

       <li>Each "Rather Not" vote adds (one minus PITA) points to that date.  An "Is OK" vote adds 3 times (one minus PITA), and a "Works Great" adds 4 times (4 times PITA).

       <li>In other words, people who are more PITA (can't attend much) don't get much of a say in what the final choice will be, and people who are less PITA (can attend everything) have a bigger voice.

       </ul>

       <li>Managers and Admins can see who voted for what at any time. (Don't expect your vote to be super secret.)

    </ol>

</div>
