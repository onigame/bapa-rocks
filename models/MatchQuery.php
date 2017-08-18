<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Match]].
 *
 * @see Match
 */
class MatchQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Match[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Match|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
