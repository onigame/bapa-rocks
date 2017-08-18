<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MachinePool;

/**
 * MachinePoolSearch represents the model behind the search form about `app\models\MachinePool`.
 */
class MachinePoolSearch extends MachinePool
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pick_count', 'machine_id', 'user_id', 'session_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MachinePool::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pick_count' => $this->pick_count,
            'machine_id' => $this->machine_id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
        ]);

        return $dataProvider;
    }
}
