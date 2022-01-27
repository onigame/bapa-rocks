<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SeasonUser;

/**
 * SeasonUserSearch represents the model behind the search form about `app\models\SeasonUser`.
 */
class SeasonUserSearch extends SeasonUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            //[['notes', 'playoff_division'], 'safe'],

            //[['id', 'matchpoints', 'game_count', 'opponent_count',
            //  'match_count', 'dues', 'user_id', 'season_id'], 'required'],
            [['matchpoints', 'game_count', 'opponent_count', 'match_count', 'dues', 'playoff_rank', 'user_id',
              'forfeit_opponent_count',
              'surplus_matchpoints', 'surplus_mpo_matchpoints', 'surplus_mpo_opponent_count',
              'playoff_matchpoints', 'playoff_mpo_matchpoints', 'playoff_mpo_opponent_count',
              'season_id', 'created_at', 'updated_at'], 'integer'],
            [['mpg', 'mpo', 'previous_season_rank', 'adjusted_mpo'], 'double'],
            [['notes'], 'string', 'max' => 255],
            [['playoff_division'], 'string', 'max' => 20],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season::className(), 'targetAttribute' => ['season_id' => 'id']],

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
        $query = SeasonUser::find();

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
            'matchpoints' => $this->matchpoints,
            'game_count' => $this->game_count,
            'opponent_count' => $this->opponent_count,
            'match_count' => $this->match_count,
            'dues' => $this->dues,
            'user_id' => $this->user_id,
            'season_id' => $this->season_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'mpg' => $this->mpg,
        ]);

        $query->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }
}
