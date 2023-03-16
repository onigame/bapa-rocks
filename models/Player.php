<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use app\models\Session;
use app\models\StatusHtml;
use dektrium\user\models\User as BaseUser;

class Player extends BaseUser {

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
                   . ".  Please pay $30 via Paypal or Venmo to mbirsching@earthlink.net . Talk to Mark if you want to pay via Zelle.</p>";
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
                 ->where(["user_id" => $this->id])->orderBy(['session_id' => SORT_DESC])->one();
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
//        $answer .= $results->seed.'th';
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
      }
      if ($results->match_status == 2 or $results->match_status == 0) {

        $BD = "style='border-bottom: 1px solid black'";
        $BR = "style='border-right: 1px solid black'";
        $BDR = "style='border-right: 1px solid black;border-bottom: 1px solid black'";

        $pc = $results->session->playerCount;
        $curmatch = $results->match;
        //$opp = "opp" $result->match->getOppString($this->id);
        $opp_playernum =(3-$curmatch->playerNum);
        $elim_match = Eliminationgraph::findCode($curmatch->code);
        $opp_seed = $elim_match->getPlayerSeed($opp_playernum);
        $cur_seed = $elim_match->getPlayerSeed($curmatch->playerNum);
        $win_seed = min($opp_seed, $cur_seed);
        $lose_seed = max($opp_seed, $cur_seed);

        $opp = $curmatch->getPlayerString($opp_playernum, 1);
        $opp_prevmatch = $curmatch->getConnectedMatch($opp_playernum);
        $opp_prev1 = "(n/a)"; $opp_prev2 = "(n/a)";
        if ($opp_prevmatch != null) {
          $opp_prev1 = $opp_prevmatch->getPlayerString(1, 2);
          $opp_prev2 = $opp_prevmatch->getPlayerString(2, 2);
        }
        $win_text = "<b><i>Rank " . Eliminationgraph::seedString($win_seed) . " if you WIN...</i></b>";
        $win = $curmatch->getConnectedMatch(-1);
        $win_code = "(n/a)";
        $win_opp_text = "(n/a)";
        $win_opp_prev1 = "(n/a)";
        $win_opp_prev2 = "(n/a)";

        if ($win != null) {
          $win_code = $win->code;
          $win_prev_data = Eliminationgraph::prevDataFromOpponentSeed($win_code, $win_seed, $pc);

          $seedmap = $win->playoffSeedsToIds;
          $win_opp_seed = $win->getOppSeed($win_seed);
          $win_opp_code = $win_prev_data['code'];
          $win_opp = Match::find()->where(['session_id' => $curmatch->session->id,
                                           'code' => $win_opp_code])->one();
          if ($win_opp != null) {
            $win_opp_text = $win_opp->getPlayerString(-1, 0);
            $win_opp_prev1 = $win_opp->getPlayerString(1, 3);
            $win_opp_prev2 = $win_opp->getPlayerString(2, 3);
          } else if ($seedmap[$win_opp_seed] != null) {
            $win_opp_player = Player::find()->where(['id' => $seedmap[$win_opp_seed]])->one();
            $win_opp_text = $win_opp_player->name;
          }
        }
        $lose_text = "<b><i>Rank " . Eliminationgraph::seedString($lose_seed) . " if you LOSE...</i></b>";
        $lose = $curmatch->getConnectedMatch(0);
        $lose_code = "(n/a)";
        $lose_opp_text = "lose_opp";
        $lose_opp_prev1 = "lose_opp_prev1";
        $lose_opp_prev2 = "lose_opp_prev2";
        if ($lose != null) {
          $lose_code = $lose->code;
          $lose_prev_data = Eliminationgraph::prevDataFromOpponentSeed($lose_code, $lose_seed, $pc);

          $seedmap = $lose->playoffSeedsToIds;
          $lose_opp_seed = $lose->getOppSeed($lose_seed);

          $lose_opp_code = $lose_prev_data['code'];
          $lose_opp = Match::find()->where(['session_id' => $curmatch->session->id,
                                           'code' => $lose_opp_code])->one();
          if ($lose_opp != null) {
            $lose_opp_text = $lose_opp->getPlayerString(-1, 0);
            $lose_opp_prev1 = $lose_opp->getPlayerString(1, 2);
            $lose_opp_prev2 = $lose_opp->getPlayerString(2, 2);
          } else if ($seedmap[$lose_opp_seed] != null) {
            $lose_opp_player = Player::find()->where(['id' => $seedmap[$lose_opp_seed]])->one();
            $lose_opp_text = $lose_opp_player->name;
          }
        }

        $answer .= "<TABLE>";
        $answer .= "<TR><TD></TD><TD $BD><b>You!</b></TD></TR>";
        $answer .= "<TR><TD $BD>$opp_prev1</TD><TD $BR></TD><TD $BD>$win_text</TD></TR>";
        $answer .= "<TR><TD $BR></TD><TD $BDR>$opp</TD><TD $BR></TD></TR>";
        $answer .= "<TR><TD $BDR>$opp_prev2</TD><TD></TD><TD $BR align='right'>$win_code</TD></TR>";
        $answer .= "<TR><TD></TD><TD $BD>$win_opp_prev1</TD><TD $BR></TD></TR>";
        $answer .= "<TR><TD></TD><TD $BR></TD><TD $BDR>$win_opp_text</TD></TR>";
        $answer .= "<TR><TD></TD><TD $BDR>$win_opp_prev2</TD><TD></TD></TR>";
        $answer .= "<TR><TD $BD>$lose_opp_prev1</TD><TD></TD><TD></TD></TR>";
        $answer .= "<TR><TD $BR></TD><TD $BD>$lose_opp_text</TD><TD></TD></TR>";
        $answer .= "<TR><TD $BDR>$lose_opp_prev2</TD><TD $BR></TD><TD $BD>$lose_code</TD></TR>";
        $answer .= "<TR><TD></TD><TD $BDR>$lose_text</TD><TD></TD></TR>";
        $answer .= "</TABLE>";

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

    if ($this->profile->vaccination == 2) {
      $answer .= '<p>Your vaccination card has been verified. Thank you!</p>';
    }
    if ($this->profile->vaccination == 1) {
      $answer .= '<p>Your vaccination card has been seen by a manager but not verified. It may help to <a href="/vaccination.html">upload</a> the image of your card yourself. Thank you!</p>';
    }
    if ($this->profile->vaccination == 0) {
      $answer .= '<p class="text-danger">You have not provided a vaccination card and may not play at Pinhouse. Please <a href="/vaccination.html">upload</a> an image of your card as soon as possible.</p>';
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

  public function getVaccToggleButton() {
    if ($this->profile->vaccination == 0) return $this->buttonHtml("Seen", 1, "btn-success");
    if ($this->profile->vaccination == 1) return $this->buttonHtml("Unseen", 0, "btn-success");
    return "<span class='not-set'>Verified</span>";
  }

  public function buttonHtml($text, $newstatus, $color) {
    return Html::a( $text,
                    ["/player/vaccstatuschange",
                     'id' => $this->id,
                     'vaccstatus' => $newstatus,
                    ],
                    [
                     'title' => 'Go',
                     'data-pjax' => '1',
                     'class' => 'btn-sm '.$color,
                    ]
                  );
  }

  public function scenarios() {
    $scenarios = parent::scenarios();
    $scenarios['create'][] = 'check';
    $scenarios['update'][] = 'check';
    $scenarios['register'][] = 'check';
    return $scenarios;
  }

  public function rules() {
    $rules = parent::rules();
    $rules['checkRequired'] = ['check', 'required'];
    $rules['checkLength'] = ['check', 'string', 'max' => 7];

    return $rules;
  }

}
