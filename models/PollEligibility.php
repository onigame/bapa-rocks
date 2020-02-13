<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "polleligibility".
 *
 * @property int $id
 * @property int $season_id
 * @property int $poll_id
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Season $season
 * @property Poll $poll
 */
class PollEligibility extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'polleligibility';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['season_id', 'poll_id'], 'required'],
            [['season_id', 'poll_id', 'created_at', 'updated_at'], 'integer'],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season::className(), 'targetAttribute' => ['season_id' => 'id']],
            [['poll_id'], 'exist', 'skipOnError' => true, 'targetClass' => Poll::className(), 'targetAttribute' => ['poll_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'season_id' => 'Season ID',
            'poll_id' => 'Poll ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Season]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSeason()
    {
        return $this->hasOne(Season::className(), ['id' => 'season_id']);
    }

    /**
     * Gets query for [[Poll]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPoll()
    {
        return $this->hasOne(Poll::className(), ['id' => 'poll_id']);
    }
}
