<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vote".
 *
 * @property int $id
 * @property int $value
 * @property int $user_id
 * @property int $pollchoice_id
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $user
 * @property PollChoice $pollchoice
 */
class Vote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value', 'user_id', 'pollchoice_id', 'created_at', 'updated_at'], 'integer'],
            [['user_id', 'pollchoice_id'], 'required'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['pollchoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => PollChoice::className(), 'targetAttribute' => ['pollchoice_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'user_id' => 'User ID',
            'pollchoice_id' => 'PollChoice ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Player::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[PollChoice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPollChoice()
    {
        return $this->hasOne(PollChoice::className(), ['id' => 'pollchoice_id']);
    }
}
