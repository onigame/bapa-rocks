<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\models\PublicSeasonUser;
use app\models\SessionUser;

/**
 * PublicSeasonUserSearch represents the model behind the search form about `app\models\PublicSeasonUser`.
 */
class PublicSeasonUserSearch extends PublicSeasonUser
{
    public $playerName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'matchpoints', 'game_count', 'opponent_count', 'match_count', 
              'dues', 'playoff_rank', 'user_id', 'row_number'], 'integer'],
            [['mpg'], 'double'],
            [['playoff_division', 'notes', 'name'], 'safe'],
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
        $query = PublicSeasonUser::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
               'defaultOrder' => ['matchpoints' => SORT_DESC, 'mpg' => SORT_DESC],
               'attributes' => ['mpg', 
                                'mpo',
                                'notes', 
                                'matchpoints', 
                                'game_count', 
                                'opponent_count', 
                                'match_count',
                                'name' => [
                                   'asc' => ['profile.name' => SORT_ASC],
                                   'desc' => ['profile.name' => SORT_DESC],
                                   'label' => 'Player Name',
                                ],
                               ],
            ],
            'pagination' => [
               'pageSize' => 0,
            ],
        ]);

        $this->season_id = $params['season_id'];

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $query->joinWith(['profile']);
            return $dataProvider;
        }

        Yii::trace($this->season_id);

        $query->select([
          'id', 'notes', 'matchpoints', 'game_count', 'opponent_count', 'previous_season_rank', 'previous_season_rank', 'previous_season_rank',
          'match_count', 'dues', 'mpg', 'mpo', 's.user_id', 'season_id',
          //new Expression('@ID := @ID + 1 AS row_number'),
        ]);

        if (array_key_exists('session_id', $params)) {
          $query->andWhere([
            $params['include'], 's.user_id', SessionUser::find()->select('user_id')
                                          ->where(['session_id' => $params['session_id']])
          ]);
        }

        $query->from([
          'seasonuser s',
          //'(SELECT @ID := 0) tempr'
        ]);

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
            //'row_number' => $this->row_number,
            'mpg' => $this->mpg,
        ]);

        $query->andFilterWhere(['like', 'notes', $this->notes]);

        $query->joinWith(['profile' => function($q) {
          $q->where('profile.name LIKE "%' . $this->name . '%"');
        }]);

        return $dataProvider;
    }
}
