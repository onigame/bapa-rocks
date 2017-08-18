<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Season]].
 *
 * @see Season
 */
class SeasonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Season[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Season|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
