<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\QueueGame;

/**
 * This is the model class for table "machinestatus".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $machine_id
 * @property integer $recorder_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Machine $machine
 * @property Game $game
 * @property User $recorder
 */
class MachineStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'machinestatus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'machine_id', 'recorder_id'], 'required'],
// we use MYSQL's timestamp internal behavior, so no yii2 timestamp code.
//            [['status', 'machine_id', 'recorder_id', 'created_at', 'updated_at'], 'integer'],
            [['status', 'game_id', 'machine_id', 'recorder_id'], 'integer'],
            [['machine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Machine::className(), 'targetAttribute' => ['machine_id' => 'id']],
            [['recorder_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['recorder_id' => 'id']],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => Game::className(), 'targetAttribute' => ['game_id' => 'id']],
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
            'machine_id' => 'Machine ID',
            'game_id' => 'Game ID',
            'recorder_id' => 'User who changed status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getGame() 
   { 
       return $this->hasOne(Game::className(), ['id' => 'game_id']); 
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
    public function getRecorder()
    {
        return $this->hasOne(Player::className(), ['id' => 'recorder_id']);
    }

    /**
     * @inheritdoc
     * @return MachineStatusQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MachineStatusQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes) {
        if ($this->status == 3 || $this->status == 4) {
          // machine is now broken or gone, remove any queued games.
          foreach (QueueGame::find()->where(['machine_id' => $this->machine_id])->all() as $queuegame) {
            $queuegame->game->cancelSelection();
          }
          // Remove any games that were in progress on it.
          foreach (Game::find()->where(['machine_id' => $this->machine_id, 'status' => 3])->all() as $progressgame) {
            $progressgame->disqualifySelection();
          }
        } else if ($this->status == 1) {
          // machine is repaired, see if any groups can be put on it.
          $this->machine->maybeStartRegularSeasonGame();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function behaviors()
    {
        return [
/*
// we use MYSQL's timestamp internal behavior, so no yii2 timestamp code.
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
*/
        ];
    }
}
