<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pvp".
 *
 * @property integer $season_id
 * @property string $season_name
 * @property integer $session_id
 * @property string $session_name
 * @property integer $game_number
 * @property integer $game_id
 * @property string $match_code
 * @property integer $match_id
 * @property integer $p1_id
 * @property string $p1_name
 * @property string $p1_score
 * @property integer $p1_score_id
 * @property integer $p1_matchpoints
 * @property integer $p1_forfeit
 * @property integer $p2_id
 * @property string $p2_name
 * @property string $p2_score
 * @property integer $p2_score_id
 * @property integer $p2_matchpoints
 * @property integer $p2_forfeit
 * @property integer $p1_win
 * @property integer $p2_win
 * @property integer $winner_id
 * @property string $winner_name
 * @property integer $machine_id
 * @property string $machine_name
 */
class Pvp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pvp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['season_id', 'session_id', 'game_number', 'game_id', 'match_id', 'p1_id', 'p1_score', 'p1_score_id', 'p1_matchpoints', 'p1_forfeit', 'p2_id', 'p2_score', 'p2_score_id', 'p2_matchpoints', 'p2_forfeit', 'p1_win', 'p2_win', 'winner_id', 'machine_id'], 'integer'],
            [['season_name', 'session_name', 'game_number', 'match_code', 'p1_id', 'p1_forfeit', 'p2_id', 'p2_forfeit'], 'required'],
            [['season_name', 'session_name', 'match_code', 'p1_name', 'p2_name', 'winner_name', 'machine_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'season_name' => 'Season Name',
            'session_id' => 'Session ID',
            'session_name' => 'Session Name',
            'game_number' => 'Game Number',
            'game_id' => 'Game ID',
            'match_code' => 'Match Code',
            'match_id' => 'Match ID',
            'p1_id' => 'P1 ID',
            'p1_name' => 'P1 Name',
            'p1_score' => 'P1 Score',
            'p1_score_id' => 'P1 Score ID',
            'p1_matchpoints' => 'P1 Matchpoints',
            'p1_forfeit' => 'P1 Forfeit',
            'p2_id' => 'P2 ID',
            'p2_name' => 'P2 Name',
            'p2_score' => 'P2 Score',
            'p2_score_id' => 'P2 Score ID',
            'p2_matchpoints' => 'P2 Matchpoints',
            'p2_forfeit' => 'P2 Forfeit',
            'p1_win' => 'P1 Win',
            'p2_win' => 'P2 Win',
            'winner_name' => 'Winner Name',
            'winner_id' => 'Winner ID',
            'machine_name' => 'Machine Name',
            'machine_id' => 'Machine ID',
        ];
    }
}
