<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "eliminationgraph".
 *
 * @property integer $id
 * @property string $code
 * @property string $bracket
 * @property integer $seed_p1
 * @property integer $seed_p2
 * @property string $prev_code_p1
 * @property integer $prev_win_p1
 * @property string $prev_code_p2
 * @property integer $prev_win_p2
 * @property string $next_code_winner
 * @property integer $next_position_winner
 * @property string $next_code_loser
 * @property integer $next_position_loser
 * @property integer $seed_max
 * @property integer $seed_min
 */
class Eliminationgraph extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'eliminationgraph';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'bracket'], 'required'],
            [['seed_p1', 'seed_p2', 'prev_win_p1', 'prev_win_p2', 'next_position_winner', 'next_position_loser', 'seed_max', 'seed_min'], 'integer'],
            [['code', 'prev_code_p1', 'prev_code_p2', 'next_code_winner', 'next_code_loser'], 'string', 'max' => 7],
            [['bracket'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Match Code',
            'bracket' => 'Bracket',
            'seed_p1' => 'Player 1 seed',
            'seed_p2' => 'Player 2 seed',
            'prev_code_p1' => 'Code for P1\'s Last Match',
            'prev_win_p1' => 'Was P1 a Winner?',
            'prev_code_p2' => 'Code for P2\'s Last Match',
            'prev_win_p2' => 'Was P2 a Winner?',
            'next_code_winner' => 'Code for Winner\'s Next Match',
            'next_position_winner' => 'Winner\'s Next Player Number',
            'next_code_loser' => 'Code for Loser\'s Next Match',
            'next_position_loser' => 'Loser\'s Next Player Number',
            'seed_max' => 'Maximum possible seed',
            'seed_min' => 'Minimum possible seed',
        ];
    }

    /**
     * @inheritdoc
     * @return EliminationgraphQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EliminationgraphQuery(get_called_class());
    }

    public static function findCode($code) {
      return Eliminationgraph::find()->where(['code' => $code])->one();
    }

    public static function firstMatchForSeed($seed, $numplayers) {
       return Eliminationgraph::find()
         ->where(['or', ['and', 'seed_p1='.$seed, 'seed_p2<'.$numplayers],
                        ['and', 'seed_p1<'.$numplayers, 'seed_p2='.$seed]])
         ->orderBy(['id' => SORT_ASC])
         ->one();
    }

    public static function nextWinnerMatch($curMatchCode, $playerCount) {
      $curMatch = Eliminationgraph::findCode($curMatchCode);
      $seed = $curMatch->seed_p1;
      $nextMatch = Eliminationgraph::findCode($curMatch->next_code_winner);
      while ($nextMatch != NULL && $nextMatch->getOpponentSeed($seed) >= $playerCount ) {
        $curMatch = $nextMatch;
        $nextMatch = Eliminationgraph::findCode($curMatch->next_code_winner);
      }
      return $curMatch->next_code_winner;
    }

    public static function nextLoserMatch($curMatchCode, $playerCount) {
      $curMatch = Eliminationgraph::findCode($curMatchCode);
      $seed = $curMatch->seed_p2;
      $nextMatch = Eliminationgraph::findCode($curMatch->next_code_loser);
      $answer = $curMatch->next_code_loser;
      while ($nextMatch != NULL && $nextMatch->getOpponentSeed($seed) >= $playerCount ) {
        $curMatch = $nextMatch;
        $nextMatch = Eliminationgraph::findCode($curMatch->next_code_winner);
        $answer = $curMatch->next_code_winner;
      }
      return $answer;
    }

    public function getOpponentSeed($seed) {
      if ($this->seed_p1 == $seed) return $this->seed_p2;
      if ($this->seed_p2 == $seed) return $this->seed_p1;
      throw new \yii\base\Exception($seed . " is not in " . $this->code);
    }

    public function getPlayerSeed($playernum) {
      if ($playernum == 1) return $this->seed_p1;
      if ($playernum == 2) return $this->seed_p2;
      throw new \yii\base\UserException($playernum . " is invalid player number");
    }

    public static function getPlayerSeedFor($code, $playernum) {
      $match = Eliminationgraph::findCode($code);
      return $match->getPlayerSeed($playernum);
    }

    public static function prevString($code, $playerseed, $playercount) {
      $match = Eliminationgraph::findCode($code);
      if ($match == null) {
        throw new \yii\base\UserException($code . " is invalid match code");
      }

      $prev_code = $match->prev_code_p1;
      $prev_win = $match->prev_win_p1;
      if ($match->seed_p2 == $playerseed) {
        $prev_code = $match->prev_code_p2;
        $prev_win = $match->prev_win_p2;
      }

      $prev_match = Eliminationgraph::findCode($prev_code);
      if ($prev_match != null && $prev_match->getOpponentSeed($playerseed) >= $playercount) {
        // previous round was a bye
        return Eliminationgraph::prevString($prev_code, $playerseed, $playercount);
      }

      return "[" . ($prev_win ? "Winner of " : "Loser of ") . $prev_code . "]";
    }

    public static function seedString($seed) {
      $seedp = $seed+1;
      if ($seedp % 100 == 11) return $seedp."th";
      if ($seedp % 100 == 12) return $seedp."th";
      if ($seedp % 100 == 13) return $seedp."th";
      if ($seedp % 10 == 1) return $seedp."st";
      if ($seedp % 10 == 2) return $seedp."nd";
      if ($seedp % 10 == 3) return $seedp."rd";
      return $seedp."th";
    }
}
