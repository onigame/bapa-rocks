<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "machinepool".
 *
 * @property integer $pick_count
 * @property integer $machine_id
 * @property integer $user_id
 * @property integer $session_id
 *
 * @property Machine $machine
 * @property User $user
 * @property Session $session
 */
class MachinePool extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'machinepool';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pick_count', 'machine_id', 'user_id', 'session_id'], 'required'],
            [['pick_count', 'machine_id', 'user_id', 'session_id'], 'integer'],
            [['machine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Machine::className(), 'targetAttribute' => ['machine_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['session_id'], 'exist', 'skipOnError' => true, 'targetClass' => Session::className(), 'targetAttribute' => ['session_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pick_count' => 'Pick Count',
            'machine_id' => 'Machine ID',
            'user_id' => 'User ID',
            'session_id' => 'Session ID',
        ];
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
     * @inheritdoc
     * @return MachinePoolQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MachinePoolQuery(get_called_class());
    }

    public static function getPoolCount($session_id, $machine_id, $user_id) {
        $pool = MachinePool::find()->where(['session_id' => $session_id,
                                    'machine_id' => $machine_id,
                                    'user_id' => $user_id])->one();
      if ($pool == NULL) return 0;
      return $pool->pick_count;
    }

}
