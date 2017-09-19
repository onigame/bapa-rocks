<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "regularmatchpoints".
 *
 * @property integer $id
 * @property string $name
 * @property integer $season_id
 * @property integer $session_id
 * @property integer $match_id
 * @property integer $user_id
 * @property string $session_name
 * @property string $code
 * @property string $matchpoints
 */
class Regularmatchpoints extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regularmatchpoints';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'season_id', 'session_id', 'match_id', 'user_id'], 'integer'],
            [['season_id', 'session_id', 'match_id', 'user_id', 'session_name', 'code'], 'required'],
            [['matchpoints'], 'number'],
            [['name', 'session_name', 'code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'season_id' => 'Season ID',
            'session_id' => 'Session ID',
            'match_id' => 'Match ID',
            'user_id' => 'User ID',
            'session_name' => 'Session Name',
            'code' => 'Code',
            'matchpoints' => 'Matchpoints',
        ];
    }
}
