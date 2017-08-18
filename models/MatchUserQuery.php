<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[MatchUser]].
 *
 * @see MatchUser
 */
class MatchUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MatchUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MatchUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
