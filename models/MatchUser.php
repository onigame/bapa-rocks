<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "matchuser".
 *
 * @property integer $id
 * @property integer $matchpoints
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
            [['matchpoints', 'game_count', 'opponent_count', 'match_id', 'user_id', 'starting_playernum'], 'required'],
            [['matchpoints', 'game_count', 'opponent_count', 'match_id', 'user_id', 'created_at', 'updated_at', 'starting_playernum'], 'integer'],
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
            'game_count' => 'Game Count',
            'opponent_count' => 'Opponent Count',
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
