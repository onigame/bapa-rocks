<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pvp;

/**
 * PvpSearch represents the model behind the search form about `app\models\Pvp`.
 */
class PvpSearch extends Pvp
{

    public function rules()
    {
        return [
            [['season_id', 'session_id', 'game_number', 'game_id', 'match_id', 'p1_id', 'p1_score', 'p1_score_id', 'p1_matchpoints', 'p1_forfeit', 'p2_id', 'p2_score', 'p2_score_id', 'p2_matchpoints', 'p2_forfeit', 'p1_win', 'p2_win', 'winner_id', 'machine_id'], 'integer'],
            [['p1_id', 'p2_id'], 'required'],
            [['season_name', 'session_name', 'match_code', 'p1_name', 'p2_name', 'winner_name', 'machine_name'], 'string', 'max' => 255],
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
        $query = Pvp::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
           'attributes' => [
              'season_id',
              'season_name',
              'session_id',
              'session_name',
              'game_number',
              'game_id',
              'match_code',
              'match_id',
              'p1_id',
              'p1_name',
              'p1_score',
              'p1_score_id',
              'p2_id',
              'p2_name',
              'p2_score',
              'p2_score_id',
              'winner_name',
              'winner_id',
              'machine_id',
              'machine_name',
           ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            Yii::warning($this->getErrors());
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'p1_id' => $this->p1_id,
            'p2_id' => $this->p2_id,
            'season_id' => $this->season_id,
            'session_id' => $this->session_id,
            'game_number' => $this->game_number,
            'game_id' => $this->game_id,
            'machine_id' => $this->machine_id,
            'match_id' => $this->match_id,
            'p1_score' => $this->p1_score,
            'p2_score' => $this->p2_score,
            'winner_id' => $this->winner_id,
        ]);

        $query->andWhere('p1_name LIKE "%' . $this->p1_name . '%"');
        $query->andWhere('p2_name LIKE "%' . $this->p2_name . '%"');
        $query->andWhere('season_name LIKE "%' . $this->season_name . '%"');
        $query->andWhere('session_name LIKE "%' . $this->session_name . '%"');
        $query->andWhere('match_code LIKE "%' . $this->match_code . '%"');
        $query->andWhere('winner_name LIKE "%' . $this->winner_name . '%"');
        $query->andWhere('machine_name LIKE "%' . $this->machine_name . '%"');

        return $dataProvider;
    }
}
