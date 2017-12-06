<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pvplist".
 *
 * @property integer $p1_id
 * @property string $p1_name
 * @property integer $p2_id
 * @property string $p2_name
 */
class Pvplist extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pvplist';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['p1_id', 'p2_id'], 'required'],
            [['p1_id', 'p2_id'], 'integer'],
            [['p1_name', 'p2_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'p1_id' => 'P1 ID',
            'p1_name' => 'P1 Name',
            'p2_id' => 'P2 ID',
            'p2_name' => 'P2 Name',
        ];
    }
}
