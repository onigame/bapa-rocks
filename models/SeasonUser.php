<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "seasonuser".
 *
 * @property integer $id
 * @property string $notes
 * @property integer $matchpoints
 * @property integer $game_count
 * @property integer $opponent_count
 * @property integer $match_count
 * @property integer $dues
 * @property integer $user_id
 * @property integer $season_id
 * @property double $previous_season_rank
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $playoff_division
 * @property integer $playoff_rank
 *
 * @property double $mpg
 * @property double $mpo
 *
 * @property User $user
 * @property Season $season
 */
class SeasonUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seasonuser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['matchpoints', 'game_count', 'opponent_count',
              'match_count', 'dues', 'user_id', 'season_id'], 'required'],
            [['matchpoints', 'game_count', 'opponent_count', 'match_count', 'dues', 'playoff_rank', 'user_id',
              'forfeit_opponent_count',
              'surplus_matchpoints', 'surplus_mpo_matchpoints', 'surplus_mpo_opponent_count',
              'playoff_matchpoints', 'playoff_mpo_matchpoints', 'playoff_mpo_opponent_count',
              'season_id', 'created_at', 'updated_at'], 'integer'],
            [['mpg', 'mpo', 'previous_season_rank', 'adjusted_mpo'], 'double'],
            [['notes'], 'string', 'max' => 255],
            [['playoff_division'], 'string', 'max' => 20],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season::className(), 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'notes' => 'Notes',
            'matchpoints' => 'MP',
            'game_count' => 'Games',
            'opponent_count' => 'Opponents',

            'surplus_matchpoints' => 'S-MP',
            'surplus_mpo_matchpoints' => 'S-MPO-EM',
            'surplus_mpo_opponent_count' => 'S-MPO-EO',

            'playoff_matchpoints' => 'PQ Score',
            'playoff_game_count' => 'PQ Games',
            'playoff_opponent_count' => 'PQ Opponents',

            'match_count' => 'Matches',
            'dues' => 'Dues',
            'playoff_division' => 'Playoff Division',
            'playoff_rank' => 'Playoff Rank',
            'mpg' => 'MP/Game',
            'mpo' => 'MP/Opp.',
            'previousperformance' => 'Prev. Perf.',
//            'previous_season_rank' => 'Prev. Rank',
            'user_id' => 'User ID',
            'season_id' => 'Season ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',

            'user_name' => 'Full Name',
            'dues_string' => 'Paid?',
            'five_weeks_string' => '5 Weeks?',
            'row_number' => 'Row#',
            'recommended_division' => 'Rc.Dv',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public function getPlayerName()
    {
        return $this->profile->name;
    }

    public function getSessionUsers()
    {
        $results = [];
        foreach ($this->sessions as $session) {
          foreach (SessionUser::find()->where(['session_id' => $session->id, 'user_id' => $this->user_id])->all() as $su) {
            $results[] = $su;
          }
        }
        return $results;
    }

    public function getCompletedSessionUsers() {
      $result = $this->sessionUsers;
      foreach ($result as $key=>$su) {
        if ($su->session->status != 2) {
          unset($result[$key]);
        }
      }
      return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
      return $this->hasMany(Session::className(), ['season_id' => 'season_id'])->orderBy(['date' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeason()
    {
        return $this->hasOne(Season::className(), ['id' => 'season_id']);
    }

    public function getUser_Name() {
      return $this->user->profile->name;
    }

    public function getDues_String() {
      $result = "";
      if ($this->dues == 0) {
        $result .= "NOT Paid";
      } else if ($this->dues == 1) {
        $result .= "Paid";
      } else {
        $result .= "<ERROR>";
      }
      if (Yii::$app->user->can('GenericManagerPermission')) {
        if ($this->dues == 0) {
          $color = 'btn-success';
        } else {
          $color = 'btn-warning';
        }
        $result .= " ";
        $result .= Html::a( "Toggle",
                      ["/season-user/toggledues", 'id' => $this->id],
                      [
                        'title' => 'Go',
                        'data-pjax' => '0',
                        'class' => 'btn-sm '.$color,
                      ]
                    );
      }
      return $result;
    }

    public function getFive_Weeks_String() {
      if ($this->match_count >= 5) {
        return "Yes";
      } else {
        return "No";
      }
    }

    public function getMostRecentRegularSessionUser() {
      $answer = null;
      $sus = $this->sessionUsers;
      if ($sus == null) return null;
      foreach ($sus as $su) {
        if ($su->session->type != 1) continue;
        if ($answer == null) {
          $answer = $su;
        } else if ($answer->session->date < $su->session->date) {
          $answer = $su;
        }
      }
      return $answer;
    }

    public function getPreviousPerformance() {
      $su = $this->mostRecentRegularSessionUser;
      if ($su == null) {
        // they haven't played this season yet, so how'd they do in the playoffs?
        $ps = $this->season->previousSeason;
        if ($ps != null) {
          $psu = Seasonuser::find()->where(['season_id' => $ps->id, 'user_id' => $this->user_id])->one();;
          if ($psu != null && $psu->playoff_division === 'A') return 12;
        }
        return mt_rand(7, 10);
      } else if ($su->session->status == 2) {
        // the most recent session is finished, so what was their score?
        $mu = $su->matchUsers;
        return $mu[0]->matchpoints;
      } else {
        // the most recent session isn't finished, so what is its previous_performance?
        return $su->previous_performance;
      }
    }

    // for sorting
    public static function byPreviousSeasonRank($a, $b) {
      if ($a->previous_season_rank < $b->previous_season_rank) return -1;
      if ($a->previous_season_rank == $b->previous_season_rank) return 0;
      return 1;
    }

    // for sorting
    public static function byPlayoffRank($a, $b) {
      if ($a->playoff_division === 'A' && $b->playoff_division !== 'A') return -1;
      if ($a->playoff_division !== 'A' && $b->playoff_division === 'A') return 1;
      if ($a->playoff_division !== $b->playoff_division) {
        throw new \yii\base\UserException("Cannot handle more than 2 divisions when comparing playoff ranks.");       
      }
      return ($a->playoff_rank - $b->playoff_rank);
    }


    /**
     * @inheritdoc
     * @return SeasonUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SeasonUserQuery(get_called_class());
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
