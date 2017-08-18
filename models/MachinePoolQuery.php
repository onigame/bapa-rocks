<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[MachinePool]].
 *
 * @see MachinePool
 */
class MachinePoolQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MachinePool[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MachinePool|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
