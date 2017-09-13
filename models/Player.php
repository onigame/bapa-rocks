<?php

namespace app\models;

use Yii;
use app\models\Session;
use app\models\StatusHtml;
use dektrium\user\models\User as BaseUser;

class Player extends User {

  public function getName() {
    return $this->profile->name;
  }

  public function getMatches() {
    return $this->hasMany(\app\models\Match::className(), ['id' => 'match_id'])
                ->viaTable('matchuser', ['user_id' => 'id']);
  }

  public function getSessions() {
    return $this->hasMany(\app\models\Session::className(), ['id' => 'session_id'])
                ->viaTable('sessionuser', ['user_id' => 'id']);
  }

  public function getSeasons() {
    return $this->hasMany(\app\models\Season::className(), ['id' => 'season_id'])
                ->viaTable('seasonuser', ['user_id' => 'id']);
  }

  public function getStatusHtml() {
    $answer = "";
    // is the player currently in a game?
    $results = Regularresults::find()
                 ->where(["user_id" => $this->id])->one();
    if ($results != NULL) {
      if ($results->session->status == 2) {
        $answer .= "<p>You last played in " . $results->session->season->name . " " 
                    . $results->session->name 
                    . " at " . $results->session->location->name
                    . ".</p>";
      } else {
        $answer .= "<p>You are playing in " . $results->session->season->name . " " 
                    . $results->session->name 
                    . " at " . $results->session->location->name
                    . ".</p>";
      }
      if ($results->match_status == 3) {
        $answer .= "<p>";
        $answer .= "You are done with that session.  Your final score was ";
        $answer .= $results->matchUser->matchpoints;
        $answer .= " in ";
        $answer .= $results->match->code;
        $answer .= ".</p>";
        // Is there an upcoming session?
        $next_session = Session::find()->where(['status' => 0])->orderBy(['date' => SORT_ASC])->one();
        if ($next_session != null) {
          $answer .= "<p>";
          $answer .= "The next session is ".$next_session->name.".  ";
          if ($next_session->currentPlayerIn) {
            $answer .= "You have signed up to play.";
          } else {
            $answer .= "You have NOT signed up to play.";
          }
          $answer .= "</p>";
        }
      } else if ($results->match_status == 2) {
        $answer .= "<p>";
        $answer .= "You are currently in ".$results->match->code." vs. ".$results->match->opponentNames;
        $answer .= "; on Game ".$results->match->gameCount.".";
        $answer .= "</p>";
        $game = $results->match->currentGame;
        if ($game == null) {
          $answer .= "Your match has no games in it! Try refreshing the page.";
          $results->match->maybeStartGame();
        } else if ($game->status == 0) {
          $answer .= Yii::$app->view->render("@app/views/game/_master_selection", ['model' => $game]);
        } else if ($game->status == 1) {
          $answer .= Yii::$app->view->render("@app/views/game/_other_selection", ['model' => $game]);
        } else if ($game->status == 2) {
          $answer .= Yii::$app->view->render("@app/views/game/_awaiting_machine", ['model' => $game]);
        } else if ($game->status == 3) {
          $answer .= $results->match->currentGame->goButton;
          $answer .= " enter scores.";
        }
      } else {
        Yii::warning($results);
        $answer .= "Something is wrong.";
      }
      return $answer;
    }

    $results = Playoffresults::find()
                 ->where(["user_id" => $this->id])->one();
    if ($results != NULL) {
      if ($results->session->status == 2) {
        $answer .= "<p>You last played in " . $results->session->season->name . " " 
                    . $results->session->name 
                    . " at " . $results->session->location->name
                    . ".</p>";
      } else {
        $answer .= "<p>You are playing in " . $results->session->season->name . " " 
                    . $results->session->name 
                    . " at " . $results->session->location->name
                    . ".</p>";
      }
      if ($results->match_status == 3) {
        $answer .= "<p>";
        $answer .= "You are done with playoffs.  Your final ranking was ";
        $nf = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);
        $answer .= $nf->format($results->seed);
        $answer .= " in Division ";
        $answer .= $results->session->playoff_division;
        $answer .= ".</p>";
      } else if ($results->match_status == 2) {
        $answer .= "<p>";
        $answer .= "You are currently in match ".$results->match->code." vs. ".$results->match->opponentNames;
        $answer .= ", on Game ".$results->match->gameCount.".";
        $answer .= "</p>";
        $game = $results->match->currentGame;
        if ($game == null) {
          $answer .= "Your match has no games in it! Try refreshing the page.";
          $results->match->maybeStartGame();
        } else if ($game->status == 0) {
          $answer .= Yii::$app->view->render("@app/views/game/_master_selection", ['model' => $game]);
        } else if ($game->status == 1) {
          $answer .= Yii::$app->view->render("@app/views/game/_other_selection", ['model' => $game]);
        } else if ($game->status == 2) {
          $answer .= Yii::$app->view->render("@app/views/game/_awaiting_machine", ['model' => $game]);
        } else if ($game->status == 3) {
          $answer .= $results->match->currentGame->goButton;
          $answer .= " enter scores.";
        }
      } else if ($results->match_status == 0) {
        $answer .= "<p>";
        $answer .= "You are waiting for an opponent in match ".$results->match->code.".";
        $answer .= $results->match->goButton;
        $answer .= "</p>";
      } else {
        Yii::warning($results);
        $answer .= "Something is wrong.";
      }
      return $answer;
    }
    return "You are currently not in any sessions!";
  }

}
