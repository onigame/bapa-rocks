<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use app\models\PublicSeasonUser;
use app\models\MachinePool;
use app\models\MatchUser;
use app\models\Eliminationgraph;
use yii\base\Security;

/**
 * This is the model class for table "sessionuser".
 *
 * @property integer $id
 * @property string $notes
 * @property integer $status
 * @property integer $user_id
 * @property integer $session_id
 * @property integer $recorder_id
 * @property integer $created_at
 * @property integer $previous_performance
 * @property integer $updated_at
 *
 * @property User $user
 * @property Session $session
 * @property User $recorder
 */
class SessionUser extends \yii\db\ActiveRecord
{

    public function getTiebreaker() {
      return Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sessionuser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'user_id', 'session_id', 'recorder_id'], 'required'],
            [['status', 'user_id', 'session_id', 'recorder_id', 'created_at', 'updated_at', 'previous_performance'], 'integer'],
            [['notes'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['session_id'], 'exist', 'skipOnError' => true, 'targetClass' => Session::className(), 'targetAttribute' => ['session_id' => 'id']],
            [['recorder_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['recorder_id' => 'id']],
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
            'status' => 'Status',
            'user_id' => 'User ID',
            'session_id' => 'Session ID',
            'recorder_id' => 'Recorder ID',
            'previous_performance' => 'Prev. Perf.',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'currentMatchString' => 'Current Match',
            'currentMatchAction' => 'Go',

            'seasonMatchpoints' => 'Season MP',
            'seasonmpg' => 'Season MP/Game',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Player::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Session::className(), ['id' => 'session_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecorder()
    {
        return $this->hasOne(Player::className(), ['id' => 'recorder_id']);
    }

    public function getSeason() {
      return $this->session->season;
    }

    public function getPublicSeasonUser() {
      return PublicSeasonUser::findOne(['user_id' => $this->user_id, 'season_id' => $this->season->id]);     
    }

    public function getSeasonMatchpoints() {
      return $this->publicSeasonUser->matchpoints;
    }

    public function getSeasonmpg() {
      return $this->publicSeasonUser->mpg;
    }

    public function getSelectionThreshold() {
      $machinelist = $this->session->selectableMachines;
      $result = 9999;
      foreach ($machinelist as $machine) {
        $poolcount = MachinePool::getPoolCount($this->session_id, $machine->id, $this->user_id);
        if ($poolcount < $result) {
          $result = $poolcount;
        }
      }
      return $result;
    }

    public function getSelectableMachineList() {
      $machinelist = $this->session->selectableMachines;
      $threshold = $this->selectionThreshold;
      $answer = [];
      foreach ($machinelist as $machine) {
        $poolcount = MachinePool::getPoolCount($this->session_id, $machine->id, $this->user_id);
        if ($poolcount > $threshold) continue; // chosen this machine enough times already.
        $status = $machine->string;
        $answer[$machine->id] = $machine->name;
        if ($status === "Available") {
        } else {
          $answer[$machine->id] .= " ($status)";
        }
      }
      return $answer;
    }

    public function getUnselectableMachineList() {
      $machinelist = $this->session->unselectableMachines;
      $answer = "";
      foreach ($machinelist as $mac) {
        $answer .= $mac->name;
        $answer .= " ";
      }
      return $answer;
    }

    public function getInfoButton() {
      return Html::a( "Info",
                      ["/sessionuser/view", 'id' => $this->id],
                      [
                        'title' => 'Info',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-info',
                      ]
                    );
    }

    // for sorting
    public static function byPreviousPerformance($a, $b) {
      if ($a->previous_performance == $b->previous_performance)
        return SeasonUser::byPreviousSeasonRank($a, $b);
      if ($a->previous_performance < $b->previous_performance) return 1;
      return -1;
    }

    /**
     * @inheritdoc
     * @return SessionUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SessionUserQuery(get_called_class());
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
