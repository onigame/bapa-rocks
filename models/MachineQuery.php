<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Machine]].
 *
 * @see Machine
 */
class MachineQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Machine[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Machine|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
