<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use app\models\Session;
use app\models\StatusHtml;
use dektrium\user\models\User as BaseUser;

class Player extends User {

  public function attributeLabels() {
    return [
      'initials' => Yii::t('app', 'HSI'),
    ];
  }

  public function getName() {
    return $this->profile->name;
  }

  public function getInitials() {
    return $this->profile->initials;
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

  public function getRegularStatusHtml() {
    $answer = "";
    // is the player currently in a game?
    $results = Regularresults::find()
                 ->where(["user_id" => $this->id])->orderBy(['date' => SORT_DESC])->one();
    if ($results != NULL) {
      $seasonuser = SeasonUser::find()->where(['season_id' => $results->session->season->id, 
                                               'user_id' => $this->id])->one();
      if ($seasonuser->dues == 0) {
        $answer .= "<p>You have NOT paid your dues for " 
                   . $results->session->season->name
                   . ".  Please do so at your first opportunity, via Paypal to mbirsching@earthlink.net .</p>";
      } else {
        $answer .= "<p>You have paid your dues for " 
                   . $results->session->season->name
                   . ".  Thanks!</p>";
      }
      if ($results->session->status == 2) {
        $answer .= "<p>Your last <b>regular session</b> was " . $results->session->season->name . " " 
                    . $results->session->name 
                    . " at " . Html::a($results->session->location->name, ['/location/view', 'id' => $results->session->location->id])
                    . ".</p>";
      } else {
        $answer .= "<p><b>You are playing</b> in " . $results->session->season->name . " " 
                    . $results->session->name 
                    . " at " . Html::a($results->session->location->name, ['/location/view', 'id' => $results->session->location->id])
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
        $next_sessions = Session::find()->where(['status' => 0])->orderBy(['date' => SORT_ASC])->all();
        $answer .= "<p>Upcoming Sessions:";
        foreach ($next_sessions as $next_session) {
          if ($next_session != null) {
            $answer .= "<LI>";
            $answer .= $next_session->name." (".$next_session->season->name.") @ "
                        . Html::a($next_session->location->name, ['/location/view', 'id' => $next_session->location->id])
                        . ". ";
            if ($next_session->currentPlayerIn) {
              $answer .= "You have signed up to play.";
            } else {
              $answer .= "You have NOT signed up to play.";
            }
          }
        }
        $answer .= "</p>";
      } else if ($results->match_status == 2) {
        $answer .= "<p>";
        $answer .= "You are currently in ".$results->match->code." vs. ".$results->match->opponentNames;
        $answer .= "; on Game ".$results->match->gameCount;
        if ($results->match->currentGame->machine != null) {
          $answer .= " (".$results->match->currentGame->machine->name.").";
        }
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
        $answer .= "Something is wrong about your regular results.";
      }
    }
    return $answer;
  }

  public function getPlayoffStatusHtml() {
    $answer = "";
    $results = Playoffresults::find()
                 ->where(["user_id" => $this->id])->one();
    if ($results != NULL) {
      if ($results->session->status == 2) {
        $answer .= "<p>Your last <b>playoffs</b> was " . $results->session->season->name . " " 
                    . $results->session->name 
                    . " at " . Html::a($results->session->location->name, ['/location/view', 'id' => $results->session->location->id])
                    . ".</p>";
      } else {
        $answer .= "<p><b>You are playing</b> in " . $results->session->season->name . " " 
                    . $results->session->name 
                    . " at " . Html::a($results->session->location->name, ['/location/view', 'id' => $results->session->location->id])
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
    return "<p>You are currently not in any playoffs!</p>";
  }

  public function getProfileStatusHtml() {
    $answer = "";
    if (ctype_space($this->profile->name) || $this->profile->name == '') {
      $answer .= '<p class="text-danger">Your display name is blank!  Please go to your <a href="/user/settings">profile</a> and update it so we can identify you on the listings.</p>';
    }
    if (ctype_space($this->profile->initials) || $this->profile->initials == '') {
      $answer .= '<p class="text-danger">Your high score initials are blank!  Please go to your <a href="/user/settings">profile</a> and update them so we won\'t delete your account.</p>';
    }
    if (ctype_space($this->profile->phone_number) || $this->profile->phone_number == '') {
      $answer .= '<p class="text-danger">Your phone number is blank!  Please go to your <a href="/user/settings">profile</a> and update it so we can contact you.</p>';
    }
    return $answer;
  }

  public function getStatusHtml() {
    $answer = "";
    $answer .= $this->profileStatusHtml;
    $answer .= $this->regularStatusHtml;
    $answer .= "<p></p>";
    $answer .= $this->playoffStatusHtml;
    return $answer;
  }


}
