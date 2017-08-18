<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PublicSeasonUser]].
 *
 * @see SeasonUser
 */
class PublicSeasonUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SeasonUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SeasonUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
