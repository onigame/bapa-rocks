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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
//            [['name'], 'string'],
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
        ]);

        $dataProvider->setSort([
           'attributes' => [
              'id',
              'name' => [
                 'asc' => ['profile.name' => SORT_ASC],
                 'desc' => ['profile.name' => SORT_DESC],
              ],
           ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $query->joinWith(['profile']);
            return $dataProvider;
        }

        $this->addCondition($query, 'id');
        $this->addCondition($query, 'name');

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->joinWith(['profile' => function ($q) {
          $q->where('profile.name LIKE "%' . $this->name . '%"');
        }]);

        return $dataProvider;
    }

    protected function addCondition($query, $attribute, $partialMatch = false)
    {
        if (($pos = strrpos($attribute, '.')) !== false) {
            $modelAttribute = substr($attribute, $pos + 1);
        } else {
            $modelAttribute = $attribute;
        }
     
        $value = $this->$modelAttribute;
        if (trim($value) === '') {
            return;
        }
     
        /* 
         * The following line is additionally added for right aliasing
         * of columns so filtering happen correctly in the self join
         */
        $attribute = "user.$attribute";
     
        if ($partialMatch) {
            $query->andWhere(['like', $attribute, $value]);
        } else {
            $query->andWhere([$attribute => $value]);
        }
    }
}
