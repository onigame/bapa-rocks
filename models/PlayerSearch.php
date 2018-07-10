<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Player;

/**
 * PlayerSearch represents the model behind the search form about `app\models\Player`.
 */
class PlayerSearch extends Player
{

    public $name;
    public $initials;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'confirmed_at', 'blocked_at', 'created_at', 'updated_at', 'flags', 'last_login_at'], 'integer'],
            [['name', 'initials', 'username', 'email', 'password_hash', 'auth_key', 'unconfirmed_email', 'registration_ip'], 'safe'],
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
        $query = Player::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
              'defaultOrder' => ['name' => SORT_ASC],
              'attributes' => [
                'name' => [
                  'asc' => ['profile.name' => SORT_ASC],
                  'desc' => ['profile.name' => SORT_DESC],
                  'label' => 'Name',
                ],
                'initials' => [
                  'asc' => ['profile.initials' => SORT_ASC],
                  'desc' => ['profile.initials' => SORT_DESC],
                  'label' => 'Name',
                ],
              ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $query->joinWith(['profile']);
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'confirmed_at' => $this->confirmed_at,
            'flags' => $this->flags,
            'last_login_at' => $this->last_login_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'registration_ip', $this->registration_ip]);

        $query->joinWith(['profile' => function($q) {
          $q->where('profile.name LIKE "%' . $this->name . '%"');
          $q->where('profile.initials LIKE "%' . $this->initials . '%"');
        }]);

        return $dataProvider;
    }
}
