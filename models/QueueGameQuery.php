<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[QueueGame]].
 *
 * @see QueueGame
 */
class QueueGameQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return QueueGame[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return QueueGame|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
