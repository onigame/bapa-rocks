<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[SessionUser]].
 *
 * @see SessionUser
 */
class SessionUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SessionUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SessionUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
