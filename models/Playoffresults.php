<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "playoffresults".
 *
 * @property integer $session_id
 * @property integer $user_id
 * @property integer $sessionuser_id
 * @property integer $matchuser_id
 * @property integer $match_id
 * @property string $code
 * @property integer $match_status
 * @property integer $seed_min
 * @property integer $seed_max
 * @property string $seed
 *
 * @property User $user
 * @property Session $session
 * @property Match $match
 */
class Playoffresults extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'playoffresults';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['session_id', 'user_id', 'match_id', 'code', 'match_status'], 'required'],
            [['session_id', 'user_id', 'seasonuser_id', 'sessionuser_id', 'season_id',
              'matchuser_id', 'match_id', 'match_status', 'seed_min', 'seed_max', 'seed'], 'integer'],
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
            'seasonuser_id' => 'Seasonuser ID',
            'matchuser_id' => 'Matchuser ID',
            'match_id' => 'Match ID',
            'code' => 'Code',
            'matchGoButton' => 'Go',
            'match_status' => 'Match Status',
            'seed_min' => 'Theoretical Worst Result',
            'true_seed_min' => 'Worst Result',
            'seed_max' => 'Best Result',
            'seed' => 'Seed',
            'starting_seed' => 'Start Seed',
        ];
    }

    public function getTrue_seed_min() {
      $pc = $this->session->playerCount;
      if ($pc < $this->seed_min) return $pc;
      return $this->seed_min;
    }

    public function getMatchGoButton() {
      return $this->match->goButton;
    }

    public function getSessionUserInfoButton() {
      return $this->sessionUser->infoButton;
    }

    public function getStarting_seed() {
      return $this->sessionUser->starting_seed;
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
        return $this->hasOne(SeasonUser::className(), ['id' => 'seasonuser_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatch()
    {
        return $this->hasOne(Match::className(), ['id' => 'match_id']);
    }


}
