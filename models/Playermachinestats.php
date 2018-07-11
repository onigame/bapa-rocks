<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "playermachinestats".
 *
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
            'user_id' => 'User ID',
            'machine_id' => 'Machine ID',
            'scoremax' => 'Best Score',
            'scorethirdquartile' => '75% Score',
            'scoremedian' => 'Median Score',
            'scorefirstquartile' => '25% Score',
            'scoremin' => 'Worst Score',
            'scoremaxgame_id' => 'Scoremaxgame ID',
            'scoremingame_id' => 'Scoremingame ID',
            'nonforfeitcount' => 'Games',
            'totalmatchpoints' => 'Total MP',
            'averagematchpoints' => 'Avg. MP',
            'forfeitcount' => 'Forfeits',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'machinename' => 'Machine',
            'locationname' => 'Location',
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
//        return Machine::find()->where(['id' => $this->machine_id]);
        return $this->hasOne(Machine::className(), ['id' => 'machine_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachinename()
    {
       if ($this->machine == null) return null;
       return $this->machine->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
//      return Location::find()->with('machine');
      return $this->hasOne(Location::className(), ['id' => 'location_id'])->via('machine');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationname()
    {
       if ($this->location == null) return null;
       return $this->location->name;
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

    private static function quantile_pos($maxindex, $ratio) {
        return floor($maxindex * $ratio);
    }

    private static function quantile_sawtooth($maxindex, $ratio) {
        return $maxindex * $ratio - floor($maxindex * $ratio);
    }

    /**
     * Recomputes stats for one player / machine
     */
    public static function recomputeStatsSingle($id, $machine_id) {
      $playermachinestats = Playermachinestats::find()->where(['user_id' => $id, 'machine_id' => $machine_id])->one();
      if ($playermachinestats == null) { 
        $playermachinestats = new Playermachinestats();
      }

      $playermachinestats->user_id = $id;
      $playermachinestats->machine_id = $machine_id;

      $playermachinestats->forfeitcount = Score::find()
                ->leftJoin('game', 'game.id = score.game_id')
                ->where(['user_id' => $id,
                         'game.machine_id' => $machine_id,
                         'forfeit' => 1,
                         'game.status' => 4, // only completed games count.
                        ])
                ->orderBy('forfeit DESC, value')
                ->count();

      $scores = Score::find()
                ->leftJoin('game', 'game.id = score.game_id')
                ->where(['user_id' => $id,
                         'game.machine_id' => $machine_id,
                         'forfeit' => 0,
                         'game.status' => 4, // only completed games count.
                        ])
                ->orderBy('forfeit DESC, value')
                ->all();

      $playermachinestats->nonforfeitcount = 0;
      $playermachinestats->totalmatchpoints = 0;
      $scorelist = [];

      $has_score = false;
      foreach ($scores as $score) {
        $playermachinestats->nonforfeitcount++;
        $scorelist[] = $score->value;
        if ($has_score) {
          if ($score->value > $playermachinestats->scoremax) {
            $playermachinestats->scoremax = $score->value;
            $playermachinestats->scoremaxgame_id = $score->game_id;
          } else if ($score->value < $playermachinestats->scoremin) {
            $playermachinestats->scoremin = $score->value;
            $playermachinestats->scoremingame_id = $score->game_id;
          }
        } else {
          $has_score = true;
          $playermachinestats->scoremax = $score->value;
          $playermachinestats->scoremaxgame_id = $score->game_id;
          $playermachinestats->scoremin = $score->value;
          $playermachinestats->scoremingame_id = $score->game_id;
        }
        $playermachinestats->totalmatchpoints += $score->matchpoints;
      }

      if ($has_score) {
        $mi = ($playermachinestats->nonforfeitcount - 1);  // maximum index
        $qp = Playermachinestats::quantile_pos($mi, 0.5);
        $st = Playermachinestats::quantile_sawtooth($mi, 0.5);
        $playermachinestats->scoremedian = $scorelist[$qp];
        if ($st > 0) $playermachinestats->scoremedian += floor($st * ($scorelist[$qp+1] - $scorelist[$qp]));
        $qp = Playermachinestats::quantile_pos($mi, 0.25);
        $st = Playermachinestats::quantile_sawtooth($mi, 0.25);
        $playermachinestats->scorefirstquartile = $scorelist[$qp];
        if ($st > 0) $playermachinestats->scorefirstquartile += floor($st * ($scorelist[$qp+1] - $scorelist[$qp]));
        $qp = Playermachinestats::quantile_pos($mi, 0.75);
        $st = Playermachinestats::quantile_sawtooth($mi, 0.75);
        $playermachinestats->scorethirdquartile = $scorelist[$qp];
        if ($st > 0) $playermachinestats->scorethirdquartile += floor($st * ($scorelist[$qp+1] - $scorelist[$qp]));
      }

      if ($has_score || $playermachinestats->forfeitcount > 0) {
        if (!$playermachinestats->save()) {
          Yii::error($playermachinestats->errors);
          throw new \yii\base\UserException("Error saving playermachinestats");
        }
      }
    }


}
