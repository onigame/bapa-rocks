<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Score;

/**
 * ScoreSearch represents the model behind the search form about `app\models\Score`.
 */
class ScoreSearch extends Score
{
    public $playerName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'playernumber', 'value', 'matchpoints', 'forfeit', 'verified', 'game_id', 'user_id', 'recorder_id', 'verifier_id', 'created_at', 'updated_at'], 'integer'],
            [['playerName'], 'safe'],
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
        $query = Score::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
              'attributes' => [
                'id',
                'playernumber',
                'matchpoints',
                'forfeit',
                'game_id',
                'Score' => [
                  'asc' => ['value' => SORT_ASC],
                  'desc' => ['value' => SORT_DESC],
                ],
                'playerName' => [
                  'asc' => ['profile.name' => SORT_ASC],
                  'desc' => ['profile.name' => SORT_DESC],
                  'label' => 'Player Name',
                ],
                'Season' => [
                  'asc' => ['season.name' => SORT_ASC],
                  'desc' => ['season.name' => SORT_DESC],
                ],
                'Session' => [
                  'asc' => ['session.name' => SORT_ASC],
                  'desc' => ['session.name' => SORT_DESC],
                ],
                'Match' => [
                  'asc' => ['match.code' => SORT_ASC],
                  'desc' => ['match.code' => SORT_DESC],
                ],
                'Game' => [
                  'asc' => ['game.id' => SORT_ASC],
                  'desc' => ['game.id' => SORT_DESC],
                ],
              ],
            ],

        ]);

        if (!($this->load($params) && $this->validate())) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $query->joinWith(['profile']);
            $query->joinWith(['match']);
            $query->joinWith(['session']);
            $query->joinWith(['season']);
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'playernumber' => $this->playernumber,
            'value' => $this->value,
            'matchpoints' => $this->matchpoints,
            'forfeit' => $this->forfeit,
            'verified' => $this->verified,
            'game_id' => $this->game_id,
            'user_id' => $this->user_id,
            'recorder_id' => $this->recorder_id,
            'verifier_id' => $this->verifier_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
