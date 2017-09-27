<?php

namespace app\models;

use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\Season;
use app\models\Session;
use app\models\Match;
use app\models\Player;

/**
 * This is the model class for table "regularmatchpoints".
 *
 * @property integer $id
 * @property string $name
 * @property integer $season_id
 * @property integer $session_id
 * @property integer $match_id
 * @property integer $user_id
 * @property string $session_name
 * @property string $code
 * @property string $matchpoints
 */
class Regularmatchpoints extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regularmatchpoints';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'season_id', 'session_id', 'match_id', 'user_id'], 'integer'],
            [['season_id', 'session_id', 'match_id', 'user_id', 'session_name', 'code'], 'required'],
            [['matchpoints'], 'number'],
            [['name', 'session_name', 'code'], 'string', 'max' => 255],
        ];
    }

    public function getMatch()
    {
        return $this->hasOne(Match::className(), ['id' => 'match_id']);
    }

    public function getSeason()
    {
        return $this->hasOne(Season::className(), ['id' => 'season_id']);
    }

    public function getSession()
    {
        return $this->hasOne(Session::className(), ['id' => 'session_id']);
    }

    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'user_id']);
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'season_id' => 'Season ID',
            'session_id' => 'Session ID',
            'match_id' => 'Match ID',
            'user_id' => 'User ID',
            'session_name' => 'Session Name',
            'code' => 'Code',
            'matchpoints' => 'Matchpoints',
        ];
    }

    public static function seasonArrayDataProvider($season_id) {
      $data = [];
      $items = Regularmatchpoints::find()->where(['season_id' => $season_id])->all();
      foreach ($items as $item) {
        if ($item->session->type != 1) continue;
        $groupblah = explode(" ", $item->code);
        if (count($groupblah) >= 2) {
          $groupnum = $groupblah[1];
        } else {
          $groupnum = "?";
        }
        $data[$item->user_id]['id'] = $item->user_id;
        $data[$item->user_id]['Name'] = $item->name;
        $data[$item->user_id][($item->session_name).' points'] = $item->matchpoints;
        //$data[$item->user_id][$item->session_name] = $item->matchpoints;
        $data[$item->user_id][$item->session_name] = Html::a(
                      $item->matchpoints,
                      ["/match/view",
                       'id' => $item->match_id,
                      ],
                      [
                        'title' => $item->code,
                        'data' => ['pjax' => 0],
                        //'class' => 'btn-sm btn-success',
                      ]
                    );
        $data[$item->user_id][($item->session_name).' group'] = Html::a(
                      '['.$groupnum.']',
                      ["/match/view",
                       'id' => $item->match_id,
                      ],
                      [
                        'title' => 'Go to Match',
                        'data' => ['pjax' => 0],
                        //'class' => 'btn-sm btn-success',
                      ]
                    );
        $data[$item->user_id][($item->session_name).' groupnum'] = $groupnum;
        if (!array_key_exists('Total', $data[$item->user_id])) {
          $data[$item->user_id]['Total'] = 0;
        }
        $data[$item->user_id]['Total'] += $item->matchpoints;
      }
      $arrayData = ArrayHelper::toArray($data);

      $adpinit = [
        'allModels' => $arrayData,
        'pagination' => [
          'pageSize' => 0,
        ],
        'sort' => [
          'attributes' => [
            'id',
            'Name',
            // individual week columns are added in the for loop below.
            'Total' => ['asc' => ['Total' => SORT_DESC], 'desc' => ['Total' => SORT_ASC]],
          ],
        ],
      ];

      for ($wn = 1; $wn <= 12; ++$wn) {
        $adpinit['sort']['attributes']["Week $wn"] = ['asc' => ["Week $wn points" => SORT_DESC], 'desc' => ["Week $wn points" => SORT_ASC]];
        $adpinit['sort']['attributes']["Week $wn group"] = ['asc' => ["Week $wn groupnum" => SORT_ASC], 'desc' => ["Week $wn groupnum" => SORT_DESC]];
      }

      $provider = new ArrayDataProvider($adpinit);
      return $provider;
    }
}
