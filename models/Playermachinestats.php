<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "playermachinestats".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $machine_id
 * @property string $scoremax
 * @property string $scorethirdquartile
 * @property string $scoremedian
 * @property string $scorefirstquartile
 * @property string $scoremin
 * @property integer $scoremaxgame_id
 * @property integer $scoremingame_id
 * @property integer $nonforfeitcount
 * @property integer $totalmatchpoints
 * @property double $averagematchpoints
 * @property integer $forfeitcount
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property Machine $machine
 * @property Game $scoremaxgame
 * @property Game $scoremingame
 */
class Playermachinestats extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'playermachinestats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'machine_id'], 'required'],
            [['user_id', 'machine_id', 'scoremax', 'scorethirdquartile', 'scoremedian', 'scorefirstquartile', 'scoremin', 'scoremaxgame_id', 'scoremingame_id', 'nonforfeitcount', 'totalmatchpoints', 'forfeitcount', 'created_at', 'updated_at'], 'integer'],
            [['averagematchpoints'], 'number'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['machine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Machine::className(), 'targetAttribute' => ['machine_id' => 'id']],
            [['scoremaxgame_id'], 'exist', 'skipOnError' => true, 'targetClass' => Game::className(), 'targetAttribute' => ['scoremaxgame_id' => 'id']],
            [['scoremingame_id'], 'exist', 'skipOnError' => true, 'targetClass' => Game::className(), 'targetAttribute' => ['scoremingame_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'machine_id' => 'Machine ID',
            'scoremax' => 'Scoremax',
            'scorethirdquartile' => 'Scorethirdquartile',
            'scoremedian' => 'Scoremedian',
            'scorefirstquartile' => 'Scorefirstquartile',
            'scoremin' => 'Scoremin',
            'scoremaxgame_id' => 'Scoremaxgame ID',
            'scoremingame_id' => 'Scoremingame ID',
            'nonforfeitcount' => 'Nonforfeitcount',
            'totalmatchpoints' => 'Totalmatchpoints',
            'averagematchpoints' => 'Averagematchpoints',
            'forfeitcount' => 'Forfeitcount',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
    public function getScoremaxgame()
    {
        return $this->hasOne(Game::className(), ['id' => 'scoremaxgame_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScoremingame()
    {
        return $this->hasOne(Game::className(), ['id' => 'scoremingame_id']);
    }
}
