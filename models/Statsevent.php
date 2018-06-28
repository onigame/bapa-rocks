<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "statsevent".
 *
 * @property integer $id
 * @property integer $eventtype
 * @property integer $created_at
 * @property integer $updated_at
 */
class Statsevent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statsevent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eventtype'], 'required'],
            [['eventtype', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eventtype' => 'Eventtype',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
