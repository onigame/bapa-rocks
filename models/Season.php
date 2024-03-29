<?php

namespace app\models;

use Yii;
use yii\data\ArrayDataProvider;
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
 * @property integer $playoff_qualification
 * @property integer $regular_season_length
 * @property integer $ifpa_weeks
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
            [['status', 'previous_season_id', 'created_at', 'updated_at', 
              'playoff_qualification', 'ifpa_weeks', 'regular_season_length'], 'integer'],
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
            'previous_season_id' => 'Prev. Season ID',
            'previousSeason.name' => 'Prev. Season Name',
            'playoff_qualification' => 'Weeks needed to Qualify',
            'regular_season_length' => 'Weeks in season',
            'ifpa_weeks' => 'Weeks counting for IFPA',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getStatustext() {
      if ($this->status == 0) return 'Not Started';
      if ($this->status == 1) return 'In Progress';
      if ($this->status == 2) return 'Awaiting Playoffs';
      if ($this->status == 3) return 'Playoffs Completed';
      if ($this->status == 4) return 'Aborted';
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

    public function getAllRegularSessionsCompleted() {
      foreach ($this->sessions as $session) {
        if ($session->type == 1 /* Regular */ && $session->status != 2 /* Completed */) return false;
      }
      return true;
    }

    public function getAllSessionsCompleted() {
      foreach ($this->sessions as $session) {
        if ($session->status != 2 /* Completed */) return false;
      }
      return true;
    }

    public function getCreateRegularSessionButton() {
      if ($this->allRegularSessionsCompleted) {
        return Html::a('Create Regular Session', ['create-session', 'season_id' => $this->id], ['class' => 'btn btn-success']);
      } else {
        return Html::a('Create Regular Session (!)', ['create-session', 'season_id' => $this->id], [
          'class' => 'btn btn-warning',
          'data' => [
             'confirm' => ('Not all regular sessions are completed! This will affect PP calculation!  Are you sure??'),
          ],
        ]);
      }
    }

    public function getCreatePlayoffsButton() {
      if ($this->allRegularSessionsCompleted) {
        return Html::a( 'Create Playoffs', ["/season/create-playoffs", 'season_id' => $this->id, ], [
                        'title' => 'Create Playoffs',
                        'class' => 'btn btn-success',
                      ]
        );
      } else {
        return Html::a( 'Create Playoffs (!)', ["/season/create-playoffs", 'season_id' => $this->id, ], [
                        'title' => 'Create Playoffs',
                        'class' => 'btn btn-warning',
                        'data' => [
                          'confirm' => 'Not all regular sessions are completed! This will affect seeding!  Are you sure??',
                          'method' => 'post',
                        ],
                      ]
        );
      }
    }

    public function getFinishSeasonButton() {
      if ($this->status == 4) {
        return '(aborted)';
      } else if ($this->status == 3) {
        return '(finished)';
      } else if ($this->allSessionsCompleted) {
        return Html::a( 'Finish This Season', ["/season/finish", 'id' => $this->id, ], [
                        'class' => 'btn btn-success',
                      ]
        );
      } else {
        return Html::a( 'Finish This Season (!)', ["/season/finish", 'id' => $this->id, ], [
                        'class' => 'btn btn-warning',
                        'data' => [
                          'confirm' => 'Not all sessions are completed! Are you sure??',
                          'method' => 'post',
                        ],
                      ]
        );
      }
    }

    public function previousSeasonsArrayData() {
      $data = [];
      $items = SeasonUser::find()->where(['season_id' => $this->id])->all();
      foreach ($items as $item) {
        $data[$item->user_id]['id'] = $item->user_id;
        $data[$item->user_id]['Name'] = $item->user->name;
        $data[$item->user_id]['Adj. MPO'] = $item->adjusted_mpo;

        $lastseason = SeasonUser::find()
            ->where(['and', ['user_id' => $item->user_id],
                            ['not', ['season_id' => $this->id]]])
            ->orderBy(['id' => SORT_DESC])
            ->one();
        if ($lastseason != null) {
          $data[$item->user_id]['Last Season ID'] = $lastseason->season_id;
          $data[$item->user_id]['Last Season'] = $lastseason->season->name;
          $data[$item->user_id]['LS Adj. MPO'] = $lastseason->adjusted_mpo;
          $data[$item->user_id]['Improvement'] = $item->adjusted_mpo - $lastseason->adjusted_mpo;
        } else {
          $data[$item->user_id]['Last Season'] = 'N/A';
          $data[$item->user_id]['Last Season ID'] = 0;
          $data[$item->user_id]['Improvement'] = -999;
        }
      }
      return $data;
    }

    public function previousSeasonsArrayDataProvider() {
      $arrayData = $this->previousSeasonsArrayData();

      $adpinit = [
        'allModels' => $arrayData,
        'pagination' => [ 'pageSize' => 0 ],
        'sort' => [
          'attributes' => [
            'id',
            'Name',
            'Adj. MPO',
            'Last Season',
            'LS Adj. MPO',
            'Improvement',
          ],
        ],
      ]; 

      $provider = new ArrayDataProvider($adpinit);
      return $provider;
    }

    public function getMaybeCreatePlayoffsButton() {
      if ($this->status == 3) return '(complete)';
      if ($this->status == 4) return '(aborted)';
      return $this->createPlayoffsButton;
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
// OLD CODE for when we had A Division
//        $rank = $last_a_rank + 1 + mt_rand() / mt_getrandmax() * (count($prevranked) - $last_a_rank - 1);
        $rank = mt_rand() / mt_getrandmax() * $last_a_rank + 0.5;
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
