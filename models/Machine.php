<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use app\models\QueueGame;
use app\models\Machinerecentstatus;

/**
 * This is the model class for table "machine".
 *
 * @property integer $id
 * @property string $name
 * @property string $abbreviation
 * @property integer $ipdb_id
 * @property integer $location_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Game[] $games
 * @property Location $location
 * @property Machinepool[] $machinepools
 * @property Machinestatus[] $machinestatuses
 * @property QueueGame[] $queuegames
 */
class Machine extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'machine';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'abbreviation', 'location_id'], 'required'],
            [['ipdb_id', 'location_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['abbreviation'], 'string', 'max' => 15],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
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
            'abbreviation' => 'Abbreviation',
            'ipdb_id' => 'ID number on IPDB',
            'location_id' => 'Location ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGames()
    {
        return $this->hasMany(Game::className(), ['machine_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachinepools()
    {
        return $this->hasMany(Machinepool::className(), ['machine_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachinestatuses()
    {
        return $this->hasMany(Machinestatus::className(), ['machine_id' => 'id']);
    }

    public function getMachinerecentstatus() {
        return $this->hasOne(Machinerecentstatus::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQueuegames()
    {
        return $this->hasMany(QueueGame::className(), ['machine_id' => 'id']);
    }

    public function getQueuelength() {
      return count($this->queuegames);
    }

    public function maybeStartQueuedGame() {
      $next = QueueGame::find()->where(['machine_id' => $this->id])->orderBy(['created_at' => SORT_ASC])->one();
      if ($next == null) return;  // Queue empty
      $game = $next->game;
      $next->delete();
      $game->startOrEnqueueGame();
    }

    public function maybeStartRegularSeasonGame() {
      // don't bother unless this machine is available.
      if ($this->machinerecentstatus->status != 1) return; 

      $games = Game::find()->where(['status' => 6])->orderBy(['created_at' => SORT_ASC])->all();
      foreach ($games as $game) {
        // don't start a game that's at a different location.
        if ($game->session->location_id != $this->location_id) continue;

        // don't start a game if the group has already played this machine.
        if ($game->match->alreadyPlayed($this)) continue;

        // okay, we'll start this game
        $game->startOnMachine($this);

        // don't bother with any other games.
        return;
      }
    }

    public function buttonHtml($text, $newstatus, $color) {
      return Html::a( $text,
                      ["/machine/statuschange", 
                       'id' => $this->id,
                       'status' => $newstatus,
                      ],
                      [
                        'title' => 'Go',
                        'data-pjax' => '1',
                        'class' => 'btn-sm '.$color,
                      ]
                    );
    }

    /**
     * @inheritdoc
     * @return MachineQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MachineQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
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
