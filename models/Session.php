<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use app\models\SessionUser;
use app\models\MatchUser;

/**
 * This is the model class for table "session".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $status
 * @property string $playoff_division
 * @property integer $season_id
 * @property integer $location_id
 * @property string $name
 * @property integer $date
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Machinepool[] $machinepools
 * @property Match[] $matches
 * @property Season $season
 * @property Location $location
 * @property Sessionuser[] $sessionusers
 */
class Session extends \yii\db\ActiveRecord
{
    public $playoffdata;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'season_id', 'location_id', 'name', 'date'], 'required'],
            [['type', 'status', 'season_id', 'location_id', 'date', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['playoff_division'], 'string', 'max' => 20],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season::className(), 'targetAttribute' => ['season_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
            [['playoffdata'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'status' => 'Status',
            'season_id' => 'Season ID',
            'location_id' => 'Location ID',
            'name' => 'Name',
            'date' => 'Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'playoffdata' => 'Playoff Data (load from checkboxes)',
            'joinButton' => 'Am I in?',

            'seasonName' => 'Season',
            'typeName' => 'Type',
            'statusString' => 'Status',
        ];
    }

    public function getTypeName() {
      if ($this->type == 1) {
        return "Regular";
      } else if ($this->type == 2) {
        return "Playoff";
      } else {
        return $this->type;
      }
    }

    public function getStatusString() {
      if ($this->status == 0) {
        return "Not Started";
      } else if ($this->status == 1) {
        return "In Progress";
      } else if ($this->status == 2) {
        return "Completed";
      } else {
        return $this->status;
      }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachinepools()
    {
        return $this->hasMany(Machinepool::className(), ['session_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatches()
    {
        return $this->hasMany(Match::className(), ['session_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatchUsers()
    {
        return $this->hasMany(MatchUser::className(), ['match_id' => 'id'])->via('matches');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeason()
    {
        return $this->hasOne(Season::className(), ['id' => 'season_id']);
    }

    public function getSeasonName() {
        return Html::a( $this->season->name,
                      ["/season/view", 'id' => $this->season_id],
                      [
                        'title' => 'View Season',
                        'data-pjax' => '0',
                      ]
                    );
    }

    public function getCloseable() {
      foreach ($this->matches as $match) {
        if ($match->status != 3) return false;
      }
      return true;
    }

    public function getLateslotcount() {
      $count = 0;
      foreach ($this->matches as $match) {
        if ($match->latePlayerOkay) $count++;
      }
      return $count;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

    public function getLocationName() {
        return Html::a( $this->location->name,
                      ["/location/view", 'id' => $this->location->id],
                      [
                        'title' => 'View Location',
                        'data-pjax' => '0',
                      ]
                    );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessionusers()
    {
        return $this->hasMany(Sessionuser::className(), ['session_id' => 'id']);
    }

    public function getPlayerCount() {
       return Sessionuser::find()->where(['session_id' => $this->id])->count();
    }

    public function getAvailableMachines() {
       return $this->location->availableMachines;
    }

    public function getSelectableMachines() {
       return $this->location->selectableMachines;
    }

    public function getUnselectableMachines() {
       return $this->location->unselectableMachines;
    }

    public function getCurrentPlayerIn() {
      $su = SessionUser::find()->where(['session_id' => $this->id, 'user_id' => Yii::$app->user->id])->one();
      if ($su != null) return true;
      return false;
    }

    public function getJoinButton() {
      if ($this->currentPlayerIn) {
        return "Yes; " . Html::a( "Leave",
                      ["/session/leave", 'id' => $this->id],
                      [
                        'title' => 'Leave',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-warning',
                      ]
                    );
      } else {
        return "No; " . Html::a( "Join",
                      ["/session/join", 'id' => $this->id],
                      [
                        'title' => 'Join',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-success',
                      ]
                    );
      }
    }

    public function getGoButton() {
      return Html::a( "Go",
                      ["/session/view", 'id' => $this->id],
                      [
                        'title' => 'Go',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-success',
                      ]
                    );
    }

    public function addPlayer($seasonuser) {
      $newSessionUser = new SessionUser();
      $newSessionUser->user_id = $seasonuser->user_id;
      $newSessionUser->session_id = $this->id;
      $newSessionUser->status = 1;
      $newSessionUser->recorder_id = Yii::$app->user->id;
      $newSessionUser->previous_performance = $seasonuser->previousPerformance;
      if (!$newSessionUser->save()) {
        Yii::error($newSessionUser->errors);
        throw new \yii\base\UserException("Error saving sessionUser");
      }
    }

    public function addLatePlayer($user_id) {
      $newSessionUser = new SessionUser();
      $newSessionUser->user_id = $user_id;
      $newSessionUser->session_id = $this->id;
      $newSessionUser->status = 2;
      $newSessionUser->recorder_id = Yii::$app->user->id;
      $newSessionUser->previous_performance = 30;
      if (!$newSessionUser->save()) {
        Yii::error($newSessionUser->errors);
        throw new \yii\base\UserException("Error saving sessionUser");
      }
    }

    public function recomputeStats() {
      foreach ($this->matches as $match) {
        $match->recomputeStats();
      }
    }

    /**
     * @inheritdoc
     * @return SessionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SessionQuery(get_called_class());
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
