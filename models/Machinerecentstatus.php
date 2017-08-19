<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "machinerecentstatus".
 *
 * @property integer $id
 * @property string $name
 * @property string $abbreviation
 * @property integer $ipdb_id
 * @property integer $location_id
 * @property integer $machinestatus_id
 * @property integer $status
 * @property integer $game_id
 * @property integer $recorder_id
 * @property string $updated_at
 */
class Machinerecentstatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'machinerecentstatus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ipdb_id', 'location_id', 'machinestatus_id', 'status', 'game_id', 'recorder_id'], 'integer'],
            [['name', 'abbreviation', 'location_id'], 'required'],
            [['updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['abbreviation'], 'string', 'max' => 15],
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
            'machinestatus_id' => 'Machinestatus ID',
            'status' => 'Status',
            'game_id' => 'Game ID',
            'recorder_id' => 'User who changed status',
            'updated_at' => 'Updated At',
        ];
    }

    public function getMachinestatus() {
        return $this->hasOne(Machinestatus::className(), ['id' => 'machinestatus_id']);
    }

    public function getMachinestatuses() {
        return $this->hasMany(Machinestatus::className(), ['machine_id' => 'id']);
    }

    public function getMachine() {
        return $this->hasOne(Machine::className(), ['id' => 'id']);
    }

    public function getGame() {
        return $this->hasOne(Game::className(), ['id' => 'game_id']);
    }

    // if the machine doesn't have a status, initialize it.
    private function initBlankStatus() {
      if ($this->status == NULL) {
        $ms = new MachineStatus();
        $ms->status = 1;
        $ms->machine_id = $this->id;
        $ms->recorder_id = Yii::$app->user->id;
        if (!$ms->save()) {
          Yii::error($ms->errors);
          throw new \yii\base\UserException("Error saving ms at initBlankStatus");
        }
      }
    }

    public function getString() {
      $this->initBlankStatus();
      if ($this->status == 1) return "Available";
      if ($this->status == 2) {
        $queuelength = $this->machine->getQueuelength();
        if ($queuelength == 0) return "In play by ".$this->game->playersString;
        return "In play by ".$this->game->playersString."; (+$queuelength other groups in queue)";
      }
      if ($this->status == 3) return "Broken";
      if ($this->status == 4) return "Gone";
      return "Unknown";
    }

    public function getSelectable() {
      $this->initBlankStatus();
      if ($this->status == 1) return true;
      if ($this->status == 2) return true;
      return false;
    }

    public function getAvailable() {
      $this->initBlankStatus();
      if ($this->status == 1) return true;
      return false;
    }

    public function getCurrentMatchInfo() {
      $this->initBlankStatus();
      if ($this->status != 2) return "<span class='not-set'>(N/A)</span>";
      return $this->game->match->session->name . " : " . $this->game->match->code ." Game ".$this->game->number;
    }

    public function getPotentialGoButton() {
      $this->initBlankStatus();
      if ($this->status != 2) return "<span class='not-set'>(N/A)</span>";
      return $this->game->goButton;
    }

    public function getBrokenButton() {
      $this->initBlankStatus();
      if ($this->status == 1) return $this->machine->buttonHtml("Broken", 3, "btn-danger");
      if ($this->status == 2) return $this->machine->buttonHtml("Broken", 3, "btn-danger");
      if ($this->status == 3) return $this->machine->buttonHtml("Repaired", 1, "btn-success");
      if ($this->status == 4) return "<span class='not-set'>(N/A)</span>";
    }

    public function getGoneButton() {
      $this->initBlankStatus();
      if ($this->status == 1) return $this->machine->buttonHtml("Gone", 4, "btn-danger");
      if ($this->status == 2) return $this->machine->buttonHtml("Gone", 4, "btn-danger");
      if ($this->status == 3) return $this->machine->buttonHtml("Gone", 4, "btn-danger");
      if ($this->status == 4) return $this->machine->buttonHtml("Returned", 1, "btn-success");
    }

}
