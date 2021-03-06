<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use app\models\MachineStatus;

/**
 * This is the model class for table "location".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $contact
 * @property string $notes
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Machine[] $machines
 * @property Session[] $sessions
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'address', 'contact', 'notes'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'address', 'contact', 'notes'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['address'], 'unique'],
            [['contact'], 'unique'],
            [['notes'], 'unique'],
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
            'address' => 'Address',
            'contact' => 'Contact',
            'notes' => 'Notes',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachines()
    {
        return $this->hasMany(Machine::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachinerecentstatuses()
    {
        return $this->hasMany(Machinerecentstatus::className(), ['location_id' => 'id'])->orderBy(['updated_at' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachinerecentstatusesByName()
    {
        return $this->hasMany(Machinerecentstatus::className(), ['location_id' => 'id'])->orderBy(['name' => SORT_ASC]);
    }

    public function getSelectableMachines() {
      $machines = $this->machinerecentstatusesByName;
      $result = [];
      foreach ($machines as $machine) {
        if ($machine->selectable) {
          $result[] = $machine;
        }
      }
      return $result;
    }

    public function getUnselectableMachines() {
      $machines = $this->machinerecentstatusesByName;
      $result = [];
      foreach ($machines as $machine) {
        if (!$machine->selectable) {
          $result[] = $machine;
        }
      }
      return $result;
    }

    public function getAvailableMachines() {
      $machines = $this->machinerecentstatuses;
      $result = [];
      foreach ($machines as $machine) {
        if ($machine->available) {
          $result[] = $machine;
        }
      }
      return $result;
    }

    public function touchAvailableMachines() {
      $machinelist = [];
      foreach ($this->availableMachines as $machine) {
        $machinelist[] = $machine;
      }
      shuffle($machinelist);
      foreach ($machinelist as $machine) {
        $ms = new MachineStatus();
        $ms->status = 1;
        $ms->machine_id = $machine->id;
        $ms->recorder_id = Yii::$app->user->id;
        $ms->save();
      }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::className(), ['location_id' => 'id']);
    }

    public function getViewButton() {
      return Html::a( "View",
                      ["/location/view", 'id' => $this->id],
                      [
                        'title' => 'View',
                        'data-pjax' => '0',
                        'class' => 'btn-sm btn-success',
                      ]
                    );
    }


    /**
     * @inheritdoc
     * @return LocationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LocationQuery(get_called_class());
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
