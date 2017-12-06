<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pvplist;

/**
 * PvplistSearch represents the model behind the search form about `app\models\Pvplist`.
 */
class PvplistSearch extends Pvplist
{

    public function rules()
    {
        return [
            [['p1_id', 'p2_id'], 'integer'],
            [['p1_name', 'p2_name'], 'string', 'max' => 255],
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
        $query = Pvplist::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
           'attributes' => [
              'p1_id',
              'p2_id',
              'p1_name',
              'p2_name',
           ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'p1_id' => $this->p1_id,
            'p2_id' => $this->p2_id,
        ]);

        $query->andWhere('p1_name LIKE "%' . $this->p1_name . '%"');
        $query->andWhere('p2_name LIKE "%' . $this->p2_name . '%"');
         

        return $dataProvider;
    }
}
