<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "regularresults".
 *
 * @property integer $session_id
 * @property integer $user_id
 * @property integer $sessionuser_id
 * @property integer $matchuser_id
 * @property integer $match_id
 * @property string $code
 * @property integer $match_status
 *
 * @property User $user
 * @property Session $session
 * @property Match $match
 */
class Regularresults extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regularresults';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['session_id', 'user_id', 'match_id', 'code', 'match_status'], 'required'],
            [['session_id', 'user_id', 'sessionuser_id', 'matchuser_id', 'match_id', 'match_status', 'date'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'session_id' => 'Session ID',
            'user_id' => 'User ID',
            'sessionuser_id' => 'Sessionuser ID',
            'matchuser_id' => 'Matchuser ID',
            'match_id' => 'Match ID',
            'code' => 'Code',
            'name' => 'Name',
            'date' => 'Date',
            'matchGoButton' => 'Go',
            'match_status' => 'Match Status',
        ];
    }

    public function getMatchGoButton() {
      return $this->match->goButton;
    }

    public function getSessionUserInfoButton() {
      return $this->sessionUser->infoButton;
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
    public function getSessionUser()
    {
        return $this->hasOne(SessionUser::className(), ['session_id' => 'session_id', 'user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeasonUser()
    {
      if ($this->sessionUser == null) return null;
      return $this->sessionUser->seasonUser;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatchUser()
    {
        return $this->hasOne(MatchUser::className(), ['id' => 'matchuser_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatch()
    {
        return $this->hasOne(Match::className(), ['id' => 'match_id']);
    }


}
