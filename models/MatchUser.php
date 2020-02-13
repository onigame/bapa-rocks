<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Score;
use yii\helpers\Html;

/**
 * This is the model class for table "matchuser".
 *
 * @property integer $id
 * @property integer $bonuspoints
 * @property integer $game_count
 * @property integer $opponent_count
 * @property integer $match_id
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Match $match
 * @property Player $user
 */
class MatchUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'matchuser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bonuspoints', 'game_count', 'opponent_count', 'forfeit_opponent_count', 'match_id', 'user_id', 'starting_playernum'], 'required'],
            [['bonuspoints', 'matchrank', 'game_count', 'opponent_count', 'forfeit_opponent_count', 'match_id', 'user_id', 'created_at', 'updated_at', 'starting_playernum'], 'integer'],
            [['match_id'], 'exist', 'skipOnError' => true, 'targetClass' => Match::className(), 'targetAttribute' => ['match_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'starting_playernum' => 'Starting Player Number',
            'matchpoints' => 'Matchpoints',
            'matchpointsbreakdown' => 'Breakdown',
            'bonuspoints' => 'MP Adj.',
            'game_count' => 'Game Count',
            'opponent_count' => 'Opponent Count',
            'forfeit_opponent_count' => 'Forfeit Opponent Count',
            'match_id' => 'Match ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
    public function getUser()
    {
        return $this->hasOne(Player::className(), ['id' => 'user_id']);
    }

    public function getName() {
      return $this->user->profile->name;
    }

    public function getScores() {
      if ($this->match == null) return [];
      $games = $this->match->games;
      $scores = [];
      foreach ($games as $game) {
        foreach (Score::find()->where(['game_id' => $game->id, 'user_id' => $this->user_id])->all() as $score) {
          $scores[] = $score;
        }
      }
      return $scores;
    }

    public function getMatchpoints() {
      $sum = 0;
      foreach ($this->scores as $score) {
        $sum += $score->matchpoints;
      }
      return ($sum + $this->bonuspoints);
    }

    public function getMatchpointsBreakdown() { 
      $terms = [];
      $sum = 0;
      foreach ($this->scores as $score) {
        if ($score->game->status != 4) continue; // only completed games
        $terms[] = "" . $score->matchpoints . " (" . $score->game->machine->abbreviation . ")";
        $sum += $score->matchpoints;
      }
      $result = join(" + ", $terms);
      if ($this->bonuspoints == 1) {
        $result .= " + 1 bonus";
      } else if ($this->bonuspoints == -1) {
        $result .= " - 1 malus";
      }
      return $result;
    }

    public function getAdmincolumn() {
      if (Yii::$app->user->can('GenericAdminPermission')) {
        return Html::a( $this->id,
                        ["/admin-match-user/update", 'id' => $this->id],
                        [
                          'title' => $this->id,
                          'data-pjax' => '0',
                          'class' => 'btn-sm btn-success',                                                                                                     ]
                      );
      } else {
        return "";
      }
    }

    public static function compareMatchpoints($matchuser_a, $matchuser_b) {
      $mp_a = $matchuser_a->matchpoints;
      $mp_b = $matchuser_b->matchpoints;
      if ($mp_a == $mp_b) return 0;
      if ($mp_a > $mp_b) return -1;
      if ($mp_a < $mp_b) return 1;
    }

    /**
     * @inheritdoc
     * @return MatchUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MatchUserQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes) {
        if ($insert) {
          // added a new record
          $this->match->maybeStartMatch();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function behaviors()
    {
        return [
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
