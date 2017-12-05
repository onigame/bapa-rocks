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

    public static function rawData($season_id) {
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

        if (!array_key_exists('Weeks Played', $data[$item->user_id])) {
          $data[$item->user_id]['Weeks Played'] = 0;
        }
        $data[$item->user_id]['Weeks Played'] += 1;

        if (!array_key_exists('Opponent Count', $data[$item->user_id])) {
          $data[$item->user_id]['Opponent Count'] = 0;
        }
        $data[$item->user_id]['Opponent Count'] += $item->opponent_count;

        if (!array_key_exists('Forfeit Opponent Count', $data[$item->user_id])) {
          $data[$item->user_id]['Forfeit Opponent Count'] = 0;
        }
        $data[$item->user_id]['Forfeit Opponent Count'] += $item->forfeit_opponent_count;

        if (!array_key_exists('Lowest Wk', $data[$item->user_id])) {
          $data[$item->user_id]['Lowest Wk'] = $item->matchpoints;
        } else if ($item->matchpoints <= $data[$item->user_id]['Lowest Wk']) {
          $data[$item->user_id]['2nd Lowest Wk'] = $data[$item->user_id]['Lowest Wk'];
          $data[$item->user_id]['Lowest Wk'] = $item->matchpoints;
        } else if (!array_key_exists('2nd Lowest Wk', $data[$item->user_id]) 
                   || ($item->matchpoints <= $data[$item->user_id]['2nd Lowest Wk'])) {
          $data[$item->user_id]['2nd Lowest Wk'] = $item->matchpoints;
        }

        $mpo = ($item->matchpoints - $item->forfeit_opponent_count) /
               ($item->opponent_count - $item->forfeit_opponent_count);
        if (!array_key_exists('Lowest MPO float', $data[$item->user_id])) {
          $data[$item->user_id]['Lowest MPO EM'] = $item->matchpoints - $item->forfeit_opponent_count;
          $data[$item->user_id]['Lowest MPO EO'] = $item->opponent_count - $item->forfeit_opponent_count;
          $data[$item->user_id]['Lowest MPO float'] = $mpo;
          $data[$item->user_id]['Lowest MPO'] = $data[$item->user_id]['Lowest MPO EM']
                                           .'/'.$data[$item->user_id]['Lowest MPO EO'];
        } else if ($mpo <= $data[$item->user_id]['Lowest MPO float']) {
          $data[$item->user_id]['2nd Lowest MPO EM'] = $data[$item->user_id]['Lowest MPO EM'];
          $data[$item->user_id]['2nd Lowest MPO EO'] = $data[$item->user_id]['Lowest MPO EO'];
          $data[$item->user_id]['2nd Lowest MPO float'] = $data[$item->user_id]['Lowest MPO float'];
          $data[$item->user_id]['2nd Lowest MPO'] = $data[$item->user_id]['Lowest MPO'];
          $data[$item->user_id]['Lowest MPO EM'] = $item->matchpoints - $item->forfeit_opponent_count;
          $data[$item->user_id]['Lowest MPO EO'] = $item->opponent_count - $item->forfeit_opponent_count;
          $data[$item->user_id]['Lowest MPO float'] = $mpo;
          $data[$item->user_id]['Lowest MPO'] = $data[$item->user_id]['Lowest MPO EM']
                                           .'/'.$data[$item->user_id]['Lowest MPO EO'];
        } else if (!array_key_exists('2nd Lowest MPO float', $data[$item->user_id])
                   || ($mpo <= $data[$item->user_id]['2nd Lowest MPO float'])) {
          $data[$item->user_id]['2nd Lowest MPO EM'] = $item->matchpoints - $item->forfeit_opponent_count;
          $data[$item->user_id]['2nd Lowest MPO EO'] = $item->opponent_count - $item->forfeit_opponent_count;
          $data[$item->user_id]['2nd Lowest MPO float'] = $mpo;
          $data[$item->user_id]['2nd Lowest MPO'] = $data[$item->user_id]['2nd Lowest MPO EM']
                                               .'/'.$data[$item->user_id]['2nd Lowest MPO EO'];
        }
      }

      $season = Season::find()->where(['id' => $season_id])->one();
      $weeksPlayed = $season->weeksPlayed;
      $weeksLeft = 12 - $weeksPlayed;
      $future = $weeksLeft * 18;

      foreach ($data as $key => &$datum) {
        $datum['Effective Opponent Count'] = $datum['Opponent Count'] - $datum['Forfeit Opponent Count'];
        $datum['Effective Matchpoints'] = $datum['Total'] - $datum['Forfeit Opponent Count'];
        $datum['MPO'] = $datum['Effective Matchpoints'] / $datum['Effective Opponent Count'];
        $datum['5 Weeks?'] = ($datum['Weeks Played'] >= 5) ? 'Yes' : 'No';
        if ($datum['Weeks Played'] >= 12) {
          $datum['Surplus MP'] = $datum['Lowest Wk'] + $datum['2nd Lowest Wk'];
          $datum['Surplus MPO EM'] = $datum['Lowest MPO EM'] + $datum['2nd Lowest MPO EM'];
          $datum['Surplus MPO EO'] = $datum['Lowest MPO EO'] + $datum['2nd Lowest MPO EO'];
        } else if ($datum['Weeks Played'] == 11) {
          $datum['Surplus MP'] = $datum['Lowest Wk'];
          $datum['Surplus MPO EM'] = $datum['Lowest MPO EM'];
          $datum['Surplus MPO EO'] = $datum['Lowest MPO EO'];
        } else {
          $datum['Surplus MP'] = 0;
          $datum['Surplus MPO EM'] = 0;
          $datum['Surplus MPO EO'] = 0;
        }
        $datum['Playoff Qual. Score'] = $datum['Total'] - $datum['Surplus MP'];
        $datum['Adj. MPO'] = 
              ($datum['Effective Matchpoints'] - $datum['Surplus MPO EM']) 
             /($datum['Effective Opponent Count'] - $datum['Surplus MPO EO']);

        $su = SeasonUser::find()->where(['user_id' => $key, 'season_id' => $season_id])->one();
        if ($su->dues == 0) {
          $datum['Dues Paid?'] = 'No';
        } else {
          $datum['Dues Paid?'] = 'Yes';
        }
      }

      return $data;
    }

    public static function seasonArrayDataProvider($season_id) {

      $arrayData = ArrayHelper::toArray(Regularmatchpoints::rawData($season_id));

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
            'Playoff Qual. Score' => ['asc' => ['5 Weeks?' => SORT_DESC, 'Playoff Qual. Score' => SORT_DESC], 
                                     'desc' => ['5 Weeks?' => SORT_DESC, 'Playoff Qual. Score' => SORT_ASC]],
            'Total' => ['asc' => ['Total' => SORT_DESC], 'desc' => ['Total' => SORT_ASC]],
            'Weeks Played' => ['asc' => ['Weeks Played' => SORT_DESC], 'desc' => ['Weeks Played' => SORT_ASC]],
            'Effective Opponent Count' => ['asc' => ['Effective Opponent Count' => SORT_DESC], 'desc' => ['Effective Opponent Count' => SORT_ASC]],
            'Effective Matchpoints' => ['asc' => ['Effective Matchpoints' => SORT_DESC], 'desc' => ['Effective Matchpoints' => SORT_ASC]],
            '5 Weeks?' => ['asc' => ['5 Weeks?' => SORT_DESC], 'desc' => ['5 Weeks?' => SORT_ASC]],
            'Opponent Count' => ['asc' => ['Opponent Count' => SORT_DESC], 'desc' => ['Opponent Count' => SORT_ASC]],
            'Forfeit Opponent Count' => ['asc' => ['Forfeit Opponent Count' => SORT_DESC], 'desc' => ['Forfeit Opponent Count' => SORT_ASC]],
            'Dues Paid?' => ['asc' => ['Dues Paid?' => SORT_DESC], 'desc' => ['Dues Paid?' => SORT_ASC]],
            //'MPO' => ['asc' => ['MPO' => SORT_DESC], 'desc' => ['MPO' => SORT_ASC]],
            'Adj. MPO' => ['asc' => ['5 Weeks?' => SORT_DESC, 'Adj. MPO' => SORT_DESC], 
                      'desc' => ['5 Weeks?' => SORT_DESC, 'Adj. MPO' => SORT_ASC]],
            'MPO' => ['asc' => ['5 Weeks?' => SORT_DESC, 'MPO' => SORT_DESC], 
                      'desc' => ['5 Weeks?' => SORT_DESC, 'MPO' => SORT_ASC]],
          ],
          'defaultOrder' => ['Playoff Qual. Score' => SORT_ASC],
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
