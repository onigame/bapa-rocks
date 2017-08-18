<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "score".
 *
 * @property integer $id
 * @property integer $playernumber
 * @property integer $value
 * @property integer $matchpoints
 * @property integer $forfeit
 * @property integer $verified
 * @property integer $game_id
 * @property integer $user_id
 * @property integer $recorder_id
 * @property integer $verifier_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Game $game
 * @property Player $user
 * @property Player $recorder
 * @property Player $verifier
 */
class Score extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'score';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['playernumber', 'forfeit', 'verified', 'game_id', 'user_id'], 'required'],
            [['playernumber', 'value', 'matchpoints', 'forfeit', 'verified', 'game_id', 'user_id', 'recorder_id', 'verifier_id', 'created_at', 'updated_at'], 'integer'],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => Game::className(), 'targetAttribute' => ['game_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['recorder_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['recorder_id' => 'id']],
            [['verifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['verifier_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'playernumber' => 'Playernumber',
            'value' => 'Value',
            'matchpoints' => 'Matchpoints',
            'forfeit' => 'Forfeit',
            'verified' => 'Verified',
            'game_id' => 'Game ID',
            'user_id' => 'User ID',
            'recorder_id' => 'Recorder ID',
            'verifier_id' => 'Verifier ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getScoreDisplay() {
      if ($this->forfeit) {
        return "<i>FORFEIT</i>";
      } else if ($this->value == null) {
        return "<span class='not-set'>(not set)</span>";
      } else {
        return number_format($this->value);
      }
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
    public function getUser()
    {
        return $this->hasOne(Player::className(), ['id' => 'user_id']);
    }

    public function getUsername() {
      return $this->user->name;
    }

    public function getEntered() {
      return ($this->value != NULL || $this->forfeit);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecorder()
    {
        return $this->hasOne(Player::className(), ['id' => 'recorder_id']);
    }

    public function getRecordername() {
       if ($this->recorder == null) return "<span class='not-set'>(nobody)</span>";
       if ($this->recorder_id == Yii::$app->user->id) {
         return "<b>You!</b>";
       }
       return $this->recorder->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVerifier()
    {
        return $this->hasOne(Player::className(), ['id' => 'verifier_id']);
    }

    public function getVerifiername() {
      if ($this->verifier == null) return "<span class='not-set'>(nobody)</span>";
      if ($this->verifier_id == Yii::$app->user->id) {
        return "<b>You!</b>";
      }
      return $this->verifier->name;
    }

    public function getVerifycolumn() {
      if (!$this->entered) {
        return "<span class='not-set'>(no score)</span>";
      }
      if ($this->verifier_id != null && $this->recorder_id != $this->verifier_id) {
        return "<span class='not-set'>(not needed)</span>";
      } 
      if ($this->verifier_id == Yii::$app->user->id) {
        return "<span class='not-set'>(you did)</span>";
      }
      if ($this->recorder_id == Yii::$app->user->id) {
        // the recorder shouldn't Verify
        return Html::a("Verify(!)", ['/score/verify', 'id' => $this->id], [
          'class' => 'btn-sm btn-warning',
          'data' => [
             'confirm' => ('You recorded this score!  Did you get another person to verify this?'),
          ],
        ]);
      } else {
        return Html::a("Verify", ['/score/verify', 'id' => $this->id], ['class' => 'btn-sm btn-success']);
      }
    }
}
