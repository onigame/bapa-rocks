<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use app\models\MatchUser;
use app\models\Eliminationgraph;
use app\models\Location;

/**
 * This is the model class for table "match".
 *
 * @property integer $id
 * @property integer $session_id
 * @property string $code
 * @property integer $format
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Game[] $games
 * @property Session $session
 * @property Matchuser[] $matchusers
 */
class Match extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'match';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['session_id', 'code', 'format', 'status'], 'required'],
            [['session_id', 'format', 'status', 'created_at', 'updated_at'], 'integer'],
            [['code'], 'string', 'max' => 255],
            [['session_id'], 'exist', 'skipOnError' => true, 'targetClass' => Session::className(), 'targetAttribute' => ['session_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'session_id' => 'Session ID',
            'code' => 'Code',
            'format' => 'Format',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',

            'formatString' => "Format",
            'statusString' => "Status",
            'matchusersString' => "Players",
            'matchusersScoresString' => "Players (Matchpoints)",
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id'])->via('session');
    }

    public function getGoButton() {
      return Html::a( "Go",
                      ["/match/go", 'id' => $this->id],
                      [
                        'title' => 'Go',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-success',
                      ]
                    );
    }

    public function getFormatString() {
      if ($this->format == 1) {
        return "2-player, first to 2 wins (3 games max)";
      } else if ($this->format == 3) {
        return "3-player, 5 games";
      } else if ($this->format == 4) {
        return "4-player, 4 games";
      } else if ($this->format == 5) {
        return "2-player, first to 3 wins (5 games max)";
      } else if ($this->format == 7) {
        return "2-player, first to 4 wins (7 games max)";
      } else {
        return "Unknown Format, Code: " . $this->format;
      }
    }

    public function getStartingPlayerCount() {
      if ($this->format == 1) return 2;
      if ($this->format == 3) return 3;
      if ($this->format == 4) return 4;
      if ($this->format == 5) return 2;
      if ($this->format == 7) return 2;
      return 1;
    }

    public function getLatePlayerOkay() {
      if ($this->format != 3) return false;
      if ($this->gamesAllCompleted) return false;
      if ($this->currentPlayerCount != 3) return false;
      return true;
    }

    public function getStatusDetailCode() {
      if ($this->status == 0) {
        if ($this->playersFilled) {
          return 1;
        }
        return 0;
      }
      if ($this->status == 2) {
        return 3 + count($this->games);
      }
      if ($this->status == 3) return 3;
      return 2;
    }

    public function getStatusString() {
      if ($this->statusDetailCode == 0) return "Awaiting Players";
      if ($this->statusDetailCode == 1) return "Ready to Start";
      if ($this->statusDetailCode == 2) return "Unknown Status";
      if ($this->statusDetailCode == 3) return "Completed";
      if ($this->currentGame == null) return "ERROR -- in progress, but no current game";
      $curGameStatus = $this->currentGame->statusString; 
      return "On Game " . $this->currentGame->number . " ($curGameStatus)";
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGames()
    {
        return $this->hasMany(Game::className(), ['match_id' => 'id'])->orderBy(['created_at' => SORT_ASC]);
    }

    public function getMachinesPlayed() {
        // don't count DQ'd machines
      $result = [];
      foreach ($this->games as $game) {
        if ($game->status == 5) continue;
        if ($game->machine === NULL) continue;
        $result[] = $game->machine;
      }
      return $result;
    }

    public function getCurrentGame() {
        $games = $this->games;
        foreach ($games as $game) {
          if ($game->status != 4 && $game->status != 5) return $game;
        }
        return null;
    }

    public function getFullGameCount() {
        return (count($this->games));
    }

    public function getGameCount() {
        // doesn't include disqualified games
        $games = $this->games;
        $answer = 0;
        foreach ($games as $game) {
          if ($game->status != 5) $answer++;
        }
        return $answer;
    }

    public function getMaximumGameCount() {
      if ($this->format == 4) return 4;
      if ($this->format == 3) return 5;
      if ($this->format == 7) return 7;
      if ($this->format == 5) return 5;
      if ($this->format == 1) return 3;
      throw new \yii\base\UserException("Unrecognized Match Format");
    }

    public function getGamesAllCompleted() {
        // if there's a game in progress, we're not all completed.
        if ($this->currentGame != null) return false;
  
        // regular weeks have 4 games for 4 players, 5 games for 3 players.
        if ($this->format == 4) return ($this->gameCount == $this->maximumGameCount);
        if ($this->format == 3) return ($this->gameCount == $this->maximumGameCount);

        // otherwise, we need to count wins.
        $winsneeded = 2;
        if ($this->format == 5) $winsneeded = 3;
        if ($this->format == 7) $winsneeded = 4;

        $wincount = [];
        $players = $this->users;
        foreach ($players as $player) {
          $wincount[$player->id] = 0;
        }
        $games = $this->games;
        foreach ($games as $game) {
          if ($game->status == 5) continue; // skip dq'd games
          $wincount[$game->winnerId]++;
          if ($wincount[$game->winnerId] == $winsneeded) return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Session::className(), ['id' => 'session_id']);
    }

    public function getSeason()
    {
        return $this->hasOne(Season::className(), ['id' => 'season_id'])->via('session');
    }

    public function getBracket() {
      if ($this->isPlayoffs) {
        $graph = Eliminationgraph::findCode($this->code);
        if ($graph->bracket === "S") return "Championship";
        if ($graph->bracket === "W") return "Winner's";
        if ($graph->bracket === "L") return "Loser's";
        if ($graph->bracket === "C") return "Consolation";
      } else {
        return 'N/A';
      }
    }

    public function getAdmincolumn() {
      if (Yii::$app->user->can('GenericAdminPermission')) {
        return Html::a( $this->id,
                        ["/admin-match/update", 'id' => $this->id],
                        [
                          'title' => $this->id,
                          'data-pjax' => '0',
                          'class' => 'btn-sm btn-success',                                                                                                     ]
                      );
      } else {
        return "";
      }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatchusers() {
      return $this->hasMany(MatchUser::className(), ['match_id' => 'id']);
    }

    public function getSessionusers() {
      $result = [];
      foreach ($this->users as $user) {
        $result[] = SessionUser::find()->where(['session_id' => $this->session->id, 'user_id' => $user->id])->one();
      }
      return $result;
    }

    public function getUsers() {
      return $this->hasMany(Player::className(), ['id' => 'user_id'])->via('matchusers');
    }

    public function getPlayers() {
      return $this->users;
    }

    public function getOpponentNames() {
      $names = [];
      foreach ($this->users as $player) {
        if ($player->id != Yii::$app->user->id) $names[] = $player->name;
      }
      return join(", ", $names);
    }

    public function getCurrentPlayerCount() {
        return count($this->matchusers);
    }

    public function getPlayersFilled() {
      return ($this->startingPlayerCount <= $this->currentPlayerCount);
    }

    public function getIsPlayoffs() {
      return ($this->session->type == 2);
    }

    public function getPlayersString() {
      $players = $this->users;
      if (count($players) == 0) return "(nobody)";
      $list = [];
      foreach ($players as $player) {
        $list[] = $player->name;
      }
      return join(', ', $list);
    }

    public function getSeasonUsers() {
      $players = $this->players;
      $result = [];
      foreach ($players as $player) {
        $result[] = SeasonUser::find()->where(['user_id' => $player->id,
                                               'season_id' => $this->season])->one();
      }
      return $result;
    }

    public function getMatchusersString() {
        if ($this->isPlayoffs) {
          if ($this->status == 3) {
            return $this->results;
          }
          $mu = MatchUser::find()->where(['match_id' => $this->id, 'starting_playernum' => 1])->one();
          $p1_seed = Eliminationgraph::getPlayerSeedFor($this->code, 1);
          if ($mu == null) {
            $p1 = Eliminationgraph::prevString($this->code, $p1_seed, $this->session->playerCount);
          } else {
            $p1 = $mu->name;
          }
          $mu = MatchUser::find()->where(['match_id' => $this->id, 'starting_playernum' => 2])->one();
          $p2_seed = Eliminationgraph::getPlayerSeedFor($this->code, 2);
          if ($mu == null) {
            $p2 = Eliminationgraph::prevString($this->code, $p2_seed, $this->session->playerCount);
          } else {
            $p2 = $mu->name;
          }
          $p1s = Eliminationgraph::seedString($p1_seed);
          $p2s = Eliminationgraph::seedString($p2_seed);
          return "($p1s)$p1, ($p2s)$p2";
        }

        $matchusers = $this->matchusers;
        $names = [];
        foreach ($matchusers as $matchuser) {
          $names[] = $matchuser->user->profile->name;
        }
        $actualcount = count($names);

        $namelist = join(", ", $names);
        if ($actualcount >= $this->startingPlayerCount) {
          return $namelist;
        }
        if ($actualcount == 0) {
          return "(empty)";
        }
        if ($actualcount < $this->startingPlayerCount) {
          $namelist .= " (+". ($this->startingPlayerCount - $actualcount) . ")";
        }
        return $namelist;
    }

    public function getMatchusersScoresString() {
        if ($this->isPlayoffs) {
          if ($this->status == 3) {
            return $this->results;
          }
          $mu = MatchUser::find()->where(['match_id' => $this->id, 'starting_playernum' => 1])->one();
          $p1_seed = Eliminationgraph::getPlayerSeedFor($this->code, 1);
          if ($mu == null) {
            $p1 = Eliminationgraph::prevString($this->code, $p1_seed, $this->session->playerCount);
          } else {
            $p1 = $mu->name;
          }
          $mu = MatchUser::find()->where(['match_id' => $this->id, 'starting_playernum' => 2])->one();
          $p2_seed = Eliminationgraph::getPlayerSeedFor($this->code, 2);
          if ($mu == null) {
            $p2 = Eliminationgraph::prevString($this->code, $p2_seed, $this->session->playerCount);
          } else {
            $p2 = $mu->name;
          }
          $p1s = Eliminationgraph::seedString($p1_seed);
          $p2s = Eliminationgraph::seedString($p2_seed);
          return "($p1s)$p1, ($p2s)$p2";
        }

        $matchusers = $this->matchusers;
        $names = [];
        foreach ($matchusers as $matchuser) {
          $names[] = $matchuser->user->profile->name . " (" . $matchuser->matchpoints . ")";
        }
        $actualcount = count($names);

        $namelist = join(", ", $names);
        if ($actualcount >= $this->startingPlayerCount) {
          return $namelist;
        }
        if ($actualcount == 0) {
          return "(empty)";
        }
        if ($actualcount < $this->startingPlayerCount) {
          $namelist .= " (+". ($this->startingPlayerCount - $actualcount) . ")";
        }
        return $namelist;
    }

    /**
     * @inheritdoc
     * @return MatchQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MatchQuery(get_called_class());
    }

    // have we already played the machine in question?
    public function alreadyPlayed($machine_in_q) {
      foreach ($this->machinesPlayed as $machine) {
        if ($machine->id == $machine_in_q->id) return true;
      }
      return false;
    }

    // starts the match.  Should be called when the player roster fills up,
    public function maybeStartMatch() {
      // don't start the Match if we don't have full players.
      if (!$this->playersFilled) return false;
      if ($this->status == 0) {
        $this->status = 2;
        $this->save();
        $this->maybeStartGame();
      }
    }

    // starts the match.  Should be called when the player roster fills up,
    // or when the previous game has finished.
    // returns true if a game was started.
    public function maybeStartGame() {
      Yii::trace("maybeStartGame ".$this->code);
      // don't start a game if we don't have full players.
      if (!$this->playersFilled) return false;

      // don't start a game if there's a game in progress.
      $curgame = $this->currentGame; 
      if ($curgame != null) return false;

      // don't start a game if the match is completed, but do complete it.
      if ($this->gamesAllCompleted) {
        $this->completeMatch();
        return false;
      }

      // okay, let's make a game.
      $game = new Game();
      $game->match_id = $this->id;
      $game->number = $this->gameCount + 1;
      $game->status = 0;
      if (!$game->save()) {
        Yii::error($game->errors);
        throw new \yii\base\UserException("Error saving game at maybeStartGame");
      }

      $game->appointMasterSelect();

      return true;
    }

    public function eligibleFor3Bonus() {
      if (!$this->gamesallCompleted) {
        throw new \yii\base\UserException("eligibleFor3Bonus called when match not completed");
      }
      $gamecount = 0;
      foreach ($this->games as $game) {
        if ($game->status == 5) continue; // skip disqualified games
        if ($game->playerCount != 3) return false;
        $gamecount++;
      }
      if ($gamecount != 5) return false;
      return true;
    }

    // completes the Match when all games are done.
    public function completeMatch() {
      Yii::trace("completeMatch ".$this->code);
      // check just in case.
      if (!$this->gamesallCompleted) {
        throw new \yii\base\UserException("completeMatch called when match not completed");
      }
      $match = $this;
      $match::getDb()->transaction(function($db) use ($match) {
        $matchusers = $match->matchusers;

        // Figure out if any $matchusers should get bonus points.
        if ($this->eligibleFor3Bonus()) { 
          foreach ($matchusers as $matchuser) {
            $pts = $matchuser->matchpoints;
            if ($pts == 15) {
              $matchuser->bonuspoints = 1;
              if (!$matchuser->save()) {
                Yii::error($matchuser->errors);
                throw new \yii\base\UserException("Error saving matchuser at maybeStartGame");
              }
            }
            if ($pts == 5) {
              $matchuser->bonuspoints = -1;
              if (!$matchuser->save()) {
                Yii::error($matchuser->errors);
                throw new \yii\base\UserException("Error saving matchuser at maybeStartGame");
              }
            }
          }
        }
        
        // Now to give matchranks.
        foreach ($matchusers as $matchuser) {
          $mps[$matchuser->id] = $matchuser->matchpoints;
        }
        usort($matchusers, function ($a, $b) use ($mps) {
          return $mps[$b->id] - $mps[$a->id]; // safe because matchpoints are integers
        });

        $last_mp = -20; // impossible mp
        $matchrank_for_tie = -1;
        $matchrank = 0;
        foreach ($matchusers as $matchuser) {
          $matchrank++;

          if ($mps[$matchuser->id] != $last_mp) {
            // not a tie, get normal rank
            $matchuser->matchrank = $matchrank;
            $matchrank_for_tie = $matchrank;
            $last_mp = $mps[$matchuser->id];
          } else {
            $matchuser->matchrank = $matchrank_for_tie;
          }

          if (!$matchuser->save()) {
            Yii::error($matchuser->errors);
            throw new \yii\base\UserException("Error saving matchuser at maybeStartGame");
          }
        }
        $match->status = 3;
        if (!$match->save()) {
          Yii::error($match->errors);
          throw new \yii\base\UserException("Error saving match at maybeStartGame");
        }
        if ($match->isPlayoffs) {
          $match->playoffAdvancePlayers();
        }
      });
    }

    public function getResults() {
      $matchusers = MatchUser::find()->where(['match_id' => $this->id])->all();
      usort($matchusers, ['app\models\MatchUser', 'compareMatchpoints']);
      $strings = [];
      foreach ($matchusers as $matchuser) {
        $strings[] = $matchuser->user->name . " = (" . $matchuser->matchpoints . " pts)";
      }
      return join(", ", $strings);
    }

    // Advances players to next match.
    private function playoffAdvancePlayers() {
      Yii::trace('playoffAdvancePlayers called '.$this->code);
      if ($this->status != 3 || !$this->isPlayoffs) {
        throw new \yii\base\UserException("playoffAdvance called illegally");
      }

      $matchusers = MatchUser::find()->where(['match_id' => $this->id])->all();
      usort($matchusers, ['app\models\MatchUser', 'compareMatchpoints']);
      $winner_id = $matchusers[0]->user_id;
      $loser_id = $matchusers[1]->user_id;

      $winnercode = Eliminationgraph::nextWinnerMatch($this->code, $this->session->playerCount);
      $losercode = Eliminationgraph::nextLoserMatch($this->code, $this->session->playerCount);

      $winnerseed = Eliminationgraph::getPlayerSeedFor($this->code, 1);
      $loserseed = Eliminationgraph::getPlayerSeedFor($this->code, 2);

      $winnermatch = Match::find()->where(['session_id' => $this->session_id,
                                           'code' => $winnercode])->one();
      $losermatch = Match::find()->where(['session_id' => $this->session_id,
                                           'code' => $losercode])->one();

      if ($winnermatch == null) {
        // player is done, let's update the seasonuser item
        $seasonuser = SeasonUser::find()->where(['user_id' => $winner_id, 'season_id' => $this->session->season_id])->one();
        $matches = [];
        preg_match('/^ZZ0*(\d+)/', $winnercode, $matches);
        $seasonuser->playoff_rank = $matches[1];
        $seasonuser->save();
      } else {
        if ($winnermatch->currentPlayerCount >= 2) {
          Yii::warning(join(", ", [$winnermatch->id, $winner_id, $winnercode, $winnerseed]));
          throw new \yii\base\UserException("trying to add player to full match");
        }
        $winnermatch->addPlayoffPlayer($winnercode, $winner_id, $winnerseed);
      }
      if ($losermatch == null) {
        // player is done, let's update the seasonuser item
        $seasonuser = SeasonUser::find()->where(['user_id' => $loser_id, 'season_id' => $this->session->season_id])->one();
        $matches = [];
        preg_match('/^ZZ0*(\d+)/', $losercode, $matches);
        $seasonuser->playoff_rank = $matches[1];
        $seasonuser->save();
      } else {
        if ($losermatch->currentPlayerCount >= 2) {
          Yii::warning(join(", ", $winnermatch->id, $winner_id, $winnercode, $winnerseed));
          throw new \yii\base\UserException("trying to add player to full match");
        }
        $losermatch->addPlayoffPlayer($losercode, $loser_id, $loserseed);
      }
    }

    public function addPlayoffPlayer($code, $user_id, $seed) {
      Yii::trace(join(', ',['Match.addPlayoffPlayer ',$code,Player::findOne($user_id)->name,$seed]));
      $graph = Eliminationgraph::findCode($code);
      $matchuser = new MatchUser();
      if ($graph->seed_p1 == $seed) {
        $matchuser->starting_playernum = 1;
      } else if ($graph->seed_p2 == $seed) {
        $matchuser->starting_playernum = 2;
      } else {
        $matchuser->starting_playernum = -1;
        throw new \yii\base\UserException("Bad playernum -- " . $seed . " is not in " . $code);
      }
      $matchuser->bonuspoints = 0;
      $matchuser->game_count = 0;
      $matchuser->user_id = $user_id;
      $matchuser->match_id = $this->id;
      $matchuser->opponent_count = 0;
      if (!$matchuser->save()) {
        Yii::error($matchuser->errors);
        throw new \yii\base\UserException("Error saving matchuser when seed = " . $seed);
      }
    }

    public function addRegularPlayer($user_id) {
      Yii::trace(join(', ',['Match.addRegularPlayer ',Player::findOne($user_id)->name]));
      $matchuser = new MatchUser();
      $matchuser->starting_playernum = -1; // not used for regular
      $matchuser->bonuspoints = 0;
      $matchuser->game_count = 0;
      $matchuser->user_id = $user_id;
      $matchuser->match_id = $this->id;
      $matchuser->opponent_count = 0;
      if (!$matchuser->save()) {
        Yii::error($matchuser->errors);
        throw new \yii\base\UserException("Error saving matchuser when seed = " . $seed);
      }
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'bedezign\yii2\audit\AuditTrailBehavior',
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

}

