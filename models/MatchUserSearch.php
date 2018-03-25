<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MatchUser;

/**
 * MatchUserSearch represents the model behind the search form about `app\models\MatchUser`.
 */
class MatchUserSearch extends MatchUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'starting_playernum', 'bonuspoints', 'game_count', 'forfeit_opponent_count', 'opponent_count', 'match_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
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
        $query = MatchUser::find();

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
            'id' => $this->id,
            'starting_playernum' => $this->starting_playernum,
            'matchrank' => $this->matchrank,
            'game_count' => $this->game_count,
            'opponent_count' => $this->opponent_count,
            'forfeit_opponent_count' => $this->forfeit_opponent_count,
            'match_id' => $this->match_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
