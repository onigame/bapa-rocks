<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "poll".
 *
 * @property int $id
 * @property int $status
 * @property string $name
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Pollchoice[] $pollchoices
 * @property Polleligibility[] $polleligibilities
 */
class Poll extends \yii\db\ActiveRecord
{
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
            [['status', 'name'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
     * Gets query for [[Polleligibilities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPolleligibilities()
    {
        return $this->hasMany(Polleligibility::className(), ['poll_id' => 'id']);
    }
}
