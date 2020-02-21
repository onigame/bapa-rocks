<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Season;

/**
 * This is the model class for table "poll".
 *
 * @property int $id
 * @property int $status
 * @property string $name
 * @property string $description
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Pollchoice[] $pollchoices
 * @property PollEligibility[] $poll_eligibilities
 */
class Poll extends \yii\db\ActiveRecord
{
    public $dates;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'poll';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 0],
            [['name', 'description'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
            [['dates'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'name' => 'Name',
            'description' => 'Description',
            'dates' => 'Date choices (separate by commas)',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Pollchoices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPollchoices()
    {
        return $this->hasMany(Pollchoice::className(), ['poll_id' => 'id']);
    }

    /**
     * Gets query for [[PollEligibilities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPollEligibilities()
    {
        return $this->hasMany(PollEligibility::className(), ['poll_id' => 'id']);
    }

    public function getPotentialEligibilities() {
      $seasons = Season::find()->orderBy(['created_at' => SORT_DESC])->all();
      $result = [];
      foreach ($seasons as $season) {
        $result[$season->id] = $season->name;
      }
      return $result;
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
