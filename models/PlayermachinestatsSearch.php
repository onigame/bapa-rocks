<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Playermachinestats;

/**
 * PlayermachinestatsSearch represents the model behind the search form about `app\models\Playermachinestats`.
 */
class PlayermachinestatsSearch extends Playermachinestats
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'machine_id', 'scoremax', 'scorethirdquartile', 'scoremedian', 'scorefirstquartile', 'scoremin', 'scoremaxgame_id', 'scoremingame_id', 'nonforfeitcount', 'totalmatchpoints', 'forfeitcount', 'created_at', 'updated_at'], 'integer'],
            [['averagematchpoints'], 'number'],
//            [['machine', 'location', 'machinename', 'locationname'], 'safe'],
            [['machine', 'location'], 'safe'],
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
        $query = Playermachinestats::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
              'defaultOrder' => ['locationname' => SORT_ASC, 'machinename' => SORT_ASC],
              'attributes' => [
                'locationname' => [
                  'asc' => ['location.name' => SORT_ASC],
                  'desc' => ['location.name' => SORT_DESC],
                  'label' => 'Location',
                ],
                'machinename' => [
                  'asc' => ['machine.name' => SORT_ASC],
                  'desc' => ['machine.name' => SORT_DESC],
                  'label' => 'Machine',
                ],
                'playername' => [
                  'asc' => ['player.name' => SORT_ASC],
                  'desc' => ['player.name' => SORT_DESC],
                  'label' => 'Player',
                ],
              ],
            ],

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'machine_id' => $this->machine_id,
            'scoremax' => $this->scoremax,
            'scorethirdquartile' => $this->scorethirdquartile,
            'scoremedian' => $this->scoremedian,
            'scorefirstquartile' => $this->scorefirstquartile,
            'scoremin' => $this->scoremin,
            'scoremaxgame_id' => $this->scoremaxgame_id,
            'scoremingame_id' => $this->scoremingame_id,
            'nonforfeitcount' => $this->nonforfeitcount,
            'totalmatchpoints' => $this->totalmatchpoints,
            'averagematchpoints' => $this->averagematchpoints,
            'forfeitcount' => $this->forfeitcount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->joinWith(['machine' => function($q) {
          $q->where('machine.name LIKE "%' . $this->machinename . '%"');
        }]);

        $query->joinWith(['location' => function($q) {
          $q->where('location.name LIKE "%' . $this->locationname . '%"');
        }]);

        return $dataProvider;
    }
}
