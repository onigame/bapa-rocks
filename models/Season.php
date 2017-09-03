<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "season".
 *
 * @property integer $id
 * @property integer $status
 * @property string $name
 * @property integer $previous_season_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Seasonuser[] $seasonusers
 * @property Session[] $sessions
 */
class Season extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'season';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'name'], 'required'],
            [['status', 'previous_season_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'name' => 'Name',
            'previous_season_id' => 'Previous Season (used for PP calculations)',
            'previousSeason.name' => 'Previous Season (used for PP calculations)',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getStatustext() {
      if ($this->status == 0) return 'Not Started';
      if ($this->status == 1) return 'In Progress';
      if ($this->status == 2) return 'Completed';
      return '<UNKNOWN>';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeasonusers()
    {
        return $this->hasMany(Seasonuser::className(), ['season_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::className(), ['season_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreviousSeason()
    {
        return $this->hasOne(Season::className(), ['id' => 'previous_season_id']);
    }

    public function getViewButton() {
      return Html::a( 'View',
                      ["/season/view",
                       'id' => $this->id,
                      ],
                      [
                        'title' => 'View',
                        'data' => ['pjax' => 0],
                        'class' => 'btn-sm btn-success',
                      ]
                    );
    }

    public function getCreatePlayoffsButton() {
      return Html::a( 'Create Playoffs',
                      ["/season/create-playoffs",
                       'season_id' => $this->id,
                      ],
                      [
                        'title' => 'Create Playoffs',
                        'data' => ['pjax' => 0],
                        'class' => 'btn-sm btn-success',
                      ]
                    );
    }

    /**
     * @inheritdoc
     * @return SeasonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SeasonQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'bedezign\yii2\audit\AuditTrailBehavior',
            ],
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
