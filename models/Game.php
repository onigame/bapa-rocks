<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use app\models\QueueGame;
use app\models\MachineStatus;

/**
 * This is the model class for table "game".
 *
 * @property integer $id
 * @property integer $match_id
 * @property integer $machine_id
 * @property integer $number
 * @property integer $status
 * @property integer $player_order_selector
 * @property integer $machine_selector
 * @property integer $master_selector
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Match $match
 * @property Machine $machine
 * @property User $playerOrderSelector
 * @property User $machineSelector
 * @property User $masterSelector
 * @property QueueGame[] $queuegames
 * @property Score[] $scores
 */
class Game extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'game';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['match_id', 'number', 'status'], 'required'],
            [['match_id', 'machine_id', 'number', 'status', 'player_order_selector', 'machine_selector', 'master_selector', 'created_at', 'updated_at'], 'integer'],
            [['match_id'], 'exist', 'skipOnError' => true, 'targetClass' => Match::className(), 'targetAttribute' => ['match_id' => 'id']],
            [['machine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Machine::className(), 'targetAttribute' => ['machine_id' => 'id']],
            [['player_order_selector'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_order_selector' => 'id']],
            [['machine_selector'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['machine_selector' => 'id']],
            [['master_selector'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['master_selector' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'match_id' => 'Match ID',
            'machine_id' => 'Machine ID',
            'number' => 'Number',
            'status' => 'Status',
            'statusString' => 'Status',
            'playerOrderSelector.name' => 'Player Order Selector',
            'player_order_selector' => 'Player Order Selector',
            'machineSelector.name' => 'Machine Selector',
            'machine_selector' => 'Machine Selector',
            'masterSelector.name' => 'Master Selector',
            'master_selector' => 'Machine/Player order selector',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getGoButton() {
      return Html::a( "Go",
                      ["/game/go", 'id' => $this->id],
                      [
                        'title' => 'Go',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-success',
                      ]
                    );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatch()
    {
        return $this->hasOne(Match::className(), ['id' => 'match_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachine()
    {
        return $this->hasOne(Machine::className(), ['id' => 'machine_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerOrderSelector()
    {
        return $this->hasOne(Player::className(), ['id' => 'player_order_selector']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachineSelector()
    {
        return $this->hasOne(Player::className(), ['id' => 'machine_selector']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasterSelector()
    {
        return $this->hasOne(Player::className(), ['id' => 'master_selector']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQueueGames()
    {
        return $this->hasMany(QueueGame::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScores()
    {
        return $this->hasMany(Score::className(), ['game_id' => 'id'])
                    ->orderBy(['playernumber' => SORT_ASC]);
    }

    public function getStatusString() {
      if ($this->status == 0) return ("Awaiting<br>Master Selection");
      if ($this->status == 1) return ("Awaiting<br>Machine/Player-Order Selection");
      if ($this->status == 2) return ("Awaiting<br>Machine: ".$this->machine->abbreviation);
      if ($this->status == 3) return ("In Progress:<br>".$this->machine->name);
      if ($this->status == 4) return ("Completed");
      if ($this->status == 5) return ("Disqualified");
      return "Unknown Status";
    }

    public function getWinnerName() {
      $winner = Player::findOne($this->winnerId);
      if ($winner == NULL) {
        return "<span class='not-set'>(unknown)</span>";
      }
      return $winner->name;
    }

    public function getWinnerId() {
      if ($this->status != 4) return NULL;
      $scores = $this->scores;
      $top_score = -1;
      $best_so_far = NULL;
      foreach ($scores as $score) {
        if ($score->forfeit == 1) continue;
        if ($score->value > $top_score) {
          $top_score = $score->value;
          $best_so_far = $score->user_id;
        }
      }
      return $best_so_far;
    }

    public function getMachineCell() {
      $machine = $this->machine;
      if ($machine == null) {
        return "[not selected yet]";
      } else {
        return $machine->name;
      }
    }

    public function getAllEntered() {
      foreach ($this->scores as $score) {
        if (!($score->entered)) return false;
      }
      return true;
    }

    public function getFinishButton() {
      if (!($this->allEntered)) return "";
      if ($this->allVerified) {
        return Html::a("Finish Game", ['/game/finish', 'id' => $this->id], ['class' => 'btn-sm btn-success']);
      } else {
        return Html::a("Finish Game(!)", ['/game/finish', 'id' => $this->id], [
          'class' => 'btn-sm btn-warning',
          'data' => [
             'confirm' => ('Not all scores are verified! Did you make sure these scores are correct?'),
          ],
        ]);
      }
    }

    public function getAllVerified() {
      foreach ($this->scores as $score) {
        if (!($score->verified)) return false;
      }
      return true;
    }

    public function maybeCompleted() {
      if (!$this->allEntered) return false;
      if ($this->allVerified) $this->finishGame();
      return true;
    }

    public function disqualifySelection() {
      $game = $this;
      $game->status = 5;
      $game->save();
      $machine->maybeStartQueuedGame();
      $game->match->maybeStartGame();
    }

    public function cancelSelection() {
      $game = $this;
      if ($game->status == 2) {
        // waiting for a machine, we should be in the queue.
        $queuegame = QueueGame::find()->where(['game_id' => $game->id])->one();
        if ($queuegame == null) {
          Yii::$app->session->setFlash('error', "This game is not in the queue!  Error GameID=".$id);
        } else {
          $game->machine_id = NULL;
          $game->status = 1;
          $game::getDb()->transaction(function($db) use ($game, $queuegame) {
            $queuegame->delete();
            $game->save();
          });
        }
      } else if ($game->status == 3) {
        // already on a machine, in the middle of a game.
        // we need to reset the score to zero.
        $machine = $game->machine; // needed to pop the next queue person up.
        $game->machine_id = NULL;
        $game->status = 1;
        $game::getDb()->transaction(function($db) use ($game) {
          $game->save();
          foreach ($game->scores as $score) {
            $score->value = null;
            $score->save();
          }
        });
        $machine->maybeStartQueuedGame();
      } else {
        Yii::$app->session->setFlash('error', "Only an in-progress game or a queued game can select another machine.");
      }
    }

    public function startOrEnqueueGame() {
      $game = $this;
      $game::getDb()->transaction(function($db) use ($game) {
        $mrs = $game->machine->machinerecentstatus;
        if ($mrs->status == 1) {
          $machinestatus = new MachineStatus;
          $machinestatus->status = 2; // in play
          $machinestatus->game_id = $game->id;
          $machinestatus->machine_id = $mrs->id;
          $machinestatus->recorder_id = Yii::$app->user->id;
          $game->status = 3; // in progress
          if (!$game->save() || !$machinestatus->save()) {
            Yii::error($game->errors);
            Yii::error($machinestatus->errors);
            throw new \yii\base\UserException("Error saving machinestatus or game in startOrEnqueueGame");
          }
        } else if ($mrs->status == 2) {
          // need to add to queue
          $qg = new QueueGame();
          $qg->machine_id = $mrs->id;
          $qg->game_id = $game->id;
          if (!$qg->save()) {
            Yii::error($qg->errors);
            throw new \yii\base\UserException("Error saving queuegame in startOrEnqueueGame");
          }
        } else {
          throw new \yii\base\UserException("Machine ".$game->machine->name." is not open! Status = ".$mrs->status);
        }
      });
    }

    public function finishGame() {
      // we assume that all checks are done and there won't be errors.
      // we ALSO assume that there aren't any tie games.  If there are,
      // results among tied players will be indeterminate.
      $game = $this;
      $game::getDb()->transaction(function($db) use ($game) {
        $scores = Score::find()->where(['game_id' => $game->id])->orderBy(['value' => SORT_DESC])->all();
        $pcount = $game->match->playerCount;
        $cur_mp = $pcount;
        if ($pcount == 2) $cur_mp = 1;
        foreach ($scores as $score) {
          $score->matchpoints = $cur_mp;
          if (!$score->save()) {
            Yii::error($score->errors);
            throw new \yii\base\UserException("Error saving score at finishGame");
          }
          $cur_mp--;
        }
        $game->status = 4;
        if (!$game->save()) {
          Yii::error($game->errors);
          throw new \yii\base\UserException("Error saving game at finishGame");
        }
        $machinestatus = new MachineStatus();
        $machinestatus->machine_id = $game->machine_id;
        $machinestatus->status = 1;
        $machinestatus->recorder_id = Yii::$app->user->id;
        if (!$machinestatus->save()) {
          Yii::error($machinestatus->errors);
          throw new \yii\base\UserException("Error saving machinestatus at finishGame");
        }

        // now we should see if anyone is waiting in the queue for the machine.
        $game->machine->maybeStartQueuedGame();
        // and let's start a new game (or end the match).
        $game->match->maybeStartGame();
      });
    }

    public function getSession() {
      return $this->match->session;
    }

    public function getSeason() {
      return $this->session->season;
    }

    public function getMatchUsers() {
      return MatchUser::find()->where(['match_id' => $this->match_id])->all();
    }

    public function getSeasonUsers() {
      $matchusers = $this->matchUsers;
      $result = [];
      foreach ($matchusers as $matchuser) {
        $result[] = SeasonUser::find()->where(['user_id' => $matchuser->user_id, 
                                               'season_id' => $this->season])->one();
      }
      return $result;
    }

    public function getPositionInQueue() {
      if ($this->status == 3) return -1;
      if ($this->status != 2) {
        throw new \yii\base\UserException("not actually in queue");
      }
      $queuegames = QueueGame::findAll(['machine_id' => $this->machine_id]);
      $answer = 1;
      foreach ($this->machine->queuegames as $qgame) {
        if ($qgame->game_id == $this->id) return $answer;
        $answer++;
      }
      throw new \yii\base\UserException("not actually in queue");
    }

    public function getHigherPlayerId() {
      $seasonusers = $this->seasonUsers;
      if (count($seasonusers) != 2) {
        throw new \yii\base\UserException("higherplayer called when > 2 players");
      }
      // Players with more MPs in the season is higher.
      if ($seasonusers[0]->matchpoints > $seasonusers[1]->matchpoints) return $seasonusers[0]->user_id;
      if ($seasonusers[0]->matchpoints < $seasonusers[1]->matchpoints) return $seasonusers[1]->user_id;

      // In case of tie, player with better current seed is higher.
      $matchusers = $this->matchUsers;
      $seed0 = EliminationGraph::getPlayerSeedFor($this->match->code, $matchusers[0]->starting_playernum);
      $seed1 = EliminationGraph::getPlayerSeedFor($this->match->code, $matchusers[1]->starting_playernum);
      if ($seed0 < $seed1) return $matchusers[0]->user_id;
      if ($seed0 > $seed1) return $matchusers[1]->user_id;
      throw new \yii\base\UserException("2 players have same seed");
    }

    public function getLowerPlayerId() {
      $seasonusers = $this->seasonUsers;
      if (count($seasonusers) != 2) {
        throw new \yii\base\UserException("higherplayer called when > 2 players");
      }
      // Players with more MPs in the season is higher.
      if ($seasonusers[0]->matchpoints < $seasonusers[1]->matchpoints) return $seasonusers[0]->user_id;
      if ($seasonusers[0]->matchpoints > $seasonusers[1]->matchpoints) return $seasonusers[1]->user_id;

      // In case of tie, player with better current seed is higher.
      $matchusers = $this->matchUsers;
      $seed0 = EliminationGraph::getPlayerSeedFor($this->match->code, $matchusers[0]->starting_playernum);
      $seed1 = EliminationGraph::getPlayerSeedFor($this->match->code, $matchusers[1]->starting_playernum);
      if ($seed0 > $seed1) return $matchusers[0]->user_id;
      if ($seed0 < $seed1) return $matchusers[1]->user_id;
      throw new \yii\base\UserException("2 players have same seed");
    }

    // do autoselection unless this is a playoff game.
    public function appointMasterSelect() {
      if ($this->master_selector != null) {
        throw new \yii\base\UserException("Master Selector already exists!");
      }
      if ($this->isPlayoffs) {
        if ($this->number % 2 == 1) {
          $this->master_selector = $this->higherPlayerId;
        } else {
          $this->master_selector = $this->lowerPlayerId;
        }
        if (!$this->save()) {
          Yii::error($this->errors);
          throw new \yii\base\UserException("Error saving game at appointMasterSelect");
        }
        return;
      }
      Yii::error("Machine autoselection isn't implemented yet.");
      throw new \yii\base\UserException("Machine autoselection not implemented yet");
    }

    public function getIsPlayoffs() {
      return ($this->match->isPlayoffs);
    }

    public function getPlayersString() {
      return ($this->match->playersString);
    }

    /**
     * @inheritdoc
     * @return GameQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GameQuery(get_called_class());
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
