<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use app\models\SeasonUser;

/**
 * This is the model class for table "season".
 *
 * @property integer $id
 * @property integer $status
 * @property string $name
 * @property integer $previous_season_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Seasonuser[] $seasonusers
 * @property Session[] $sessions
 */
class Season extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'season';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'name'], 'required'],
            [['status', 'previous_season_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'name' => 'Name',
            'previous_season_id' => 'Previous Season (used for PP calculations)',
            'previousSeason.name' => 'Previous Season (used for PP calculations)',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getStatustext() {
      if ($this->status == 0) return 'Not Started';
      if ($this->status == 1) return 'In Progress';
      if ($this->status == 2) return 'Completed';
      return '<UNKNOWN>';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeasonusers()
    {
        return $this->hasMany(Seasonuser::className(), ['season_id' => 'id']);
    }

    public function getSeasonusersByPlayoffRank() {
      $sus = $this->seasonusers;
      foreach ($sus as $key=>$su) {
        if ($su->playoff_rank === null) {
          unset($sus[$key]);
        }
      }
      usort($sus, ['app\models\SeasonUser', 'byPlayoffRank']);
      return $sus;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::className(), ['season_id' => 'id'])->orderBy(['date' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreviousSeason()
    {
        return $this->hasOne(Season::className(), ['id' => 'previous_season_id']);
    }

    public function getWeeksPlayed() {
      $sessions = $this->sessions;
      $answer = 0;
      foreach ($sessions as $session) {
        if ($session->type == 1 && $session->status == 2) {
          $answer++;
        }
      }
      return $answer;
    }

    public function getLastLocationId() {
      $sessions = $this->sessions;
      if ($sessions == null) return null;
      return $sessions[0]->id;
    }

    public function getViewButton() {
      return Html::a( 'View',
                      ["/season/view",
                       'id' => $this->id,
                      ],
                      [
                        'title' => 'View',
                        'data' => ['pjax' => 0],
                        'class' => 'btn-sm btn-success',
                      ]
                    );
    }

    public function getCreatePlayoffsButton() {
      return Html::a( 'Create Playoffs',
                      ["/season/create-playoffs",
                       'season_id' => $this->id,
                      ],
                      [
                        'title' => 'Create Playoffs',
                        'data' => ['pjax' => 0],
                        'class' => 'btn-sm btn-success',
                      ]
                    );
    }

    public function addplayer($player_id) {
      $su = new SeasonUser();
      $su->matchpoints = 0;
      $su->game_count = 0;
      $su->opponent_count = 0;
      $su->match_count = 0;
      $su->dues = 0;
      $su->user_id = $player_id;
      $su->season_id = $this->id;
      // time to calculate the previous_season_rank
      $prevranked = $this->previousSeason->seasonusersByPlayoffRank;
      $rank = 1;
      $last_a_rank = 1;
      foreach ($prevranked as $seasonuser) {
        if ($seasonuser->user_id == $player_id) break;
        if ($seasonuser->playoff_division === "A") $last_a_rank = $rank;
        $rank++;
      }
      if ($rank > count($prevranked)) {
        $rank = $last_a_rank + 1 + mt_rand() / mt_getrandmax() * (count($prevranked) - $last_a_rank - 1);
      }
      $su->previous_season_rank = $rank;
      if (!$su->save()) {
        Yii::warning(Html::errorSummary($su));
        throw new \yii\base\UserException("Error saving in Season::addplayer");
      }
    }

    /**
     * @inheritdoc
     * @return SeasonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SeasonQuery(get_called_class());
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
