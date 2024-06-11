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

      $season = Season::find()->where(['id' => $season_id])->one();
      $weeksPlayed = $season->weeksPlayed;
      $weeksLeft = $season->regular_season_length - $weeksPlayed;
      $future = $weeksLeft * 18;

      $items = Regularmatchpoints::find()->where(['season_id' => $season_id])->all();
      foreach ($items as $item) {  // iterate through the players
        if ($item->session->type != 1) continue;
        $groupblah = explode(" ", $item->code);
        if (count($groupblah) >= 2) {
          $groupnum = $groupblah[1];
        } else {
          $groupnum = "?";
        }
        $profile = Profile::find()->where(['user_id' => $item->user_id])->one();
        $data[$item->user_id]['id'] = $item->user_id;
        $data[$item->user_id]['Name'] = $item->name;
        $data[$item->user_id]['ifpa_id'] = $profile->ifpa;
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

        $data[$item->user_id]['Weeks Absent'] = $weeksPlayed 
                                                - $data[$item->user_id]['Weeks Played'];

        $data[$item->user_id]['Attendance Bonus'] = 
            ($data[$item->user_id]['Weeks Absent'] < 3)
             ? (2 - $data[$item->user_id]['Weeks Absent'])
             : (4 * $data[$item->user_id]['Weeks Absent'] - 9);

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
          if (!array_key_exists('2nd Lowest Wk', $data[$item->user_id])) {
            $data[$item->user_id]['2nd Lowest Wk'] = $data[$item->user_id]['Lowest Wk'];
            $data[$item->user_id]['Lowest Wk'] = $item->matchpoints;
          } else { 
            $data[$item->user_id]['3rd Lowest Wk'] = $data[$item->user_id]['2nd Lowest Wk'];
            $data[$item->user_id]['2nd Lowest Wk'] = $data[$item->user_id]['Lowest Wk'];
            $data[$item->user_id]['Lowest Wk'] = $item->matchpoints;
          }
        } else if (!array_key_exists('2nd Lowest Wk', $data[$item->user_id])) {
          $data[$item->user_id]['2nd Lowest Wk'] = $item->matchpoints;
        } else if ($item->matchpoints <= $data[$item->user_id]['2nd Lowest Wk']) {
          $data[$item->user_id]['3rd Lowest Wk'] = $data[$item->user_id]['2nd Lowest Wk'];
          $data[$item->user_id]['2nd Lowest Wk'] = $item->matchpoints;
        } else if (!array_key_exists('3rd Lowest Wk', $data[$item->user_id])) {
          $data[$item->user_id]['3rd Lowest Wk'] = $item->matchpoints;
        } else if ($item->matchpoints <= $data[$item->user_id]['3rd Lowest Wk']) {
          $data[$item->user_id]['3rd Lowest Wk'] = $item->matchpoints;
        }

        if ($item->opponent_count - $item->forfeit_opponent_count == 0) {
          $mpo = 0;
        } else {
          $mpo = ($item->matchpoints - $item->forfeit_opponent_count) /
                 ($item->opponent_count - $item->forfeit_opponent_count);
        }

        if (!array_key_exists('Email', $data[$item->user_id])) {
          $data[$item->user_id]['Email'] = "no email on file";
        }
        $data[$item->user_id]['Email'] = $item->player->email;

        if (!array_key_exists('PEmail', $data[$item->user_id])) {
          $data[$item->user_id]['PEmail'] = "no email on file";
        }
        $data[$item->user_id]['PEmail'] = $item->player->profile->public_email;

        if (!array_key_exists('Phone', $data[$item->user_id])) {
          $data[$item->user_id]['Phone'] = "no phone on file";
        }
        $data[$item->user_id]['Phone'] = $item->player->profile->phone_number;

      }

      foreach ($data as $key => &$datum) {
        $datum['Effective Opponent Count'] = $datum['Opponent Count'] - $datum['Forfeit Opponent Count'];
        $datum['Effective Matchpoints'] = $datum['Total'] - $datum['Forfeit Opponent Count'];
        $datum['MPO'] = $datum['Effective Matchpoints'] / $datum['Effective Opponent Count'];
        $datum[$season->playoff_qualification . ' Weeks?'] 
            = ($datum['Weeks Played'] >= $season->playoff_qualification) ? 'Yes' : 'No';
        $datum['5 Weeks?'] = ($datum['Weeks Played'] >= 5) ? 'Yes' : 'No';

        $datum['Surplus MP'] = 0;
        if ($datum['Weeks Played'] >= $season->regular_season_length - 2) {
          $datum['Surplus MP'] += $datum['Lowest Wk'];
        }
        if ($datum['Weeks Played'] >= $season->regular_season_length - 1) {
          $datum['Surplus MP'] += $datum['2nd Lowest Wk'];
        }
        if ($datum['Weeks Played'] >= $season->regular_season_length) {
          $datum['Surplus MP'] += $datum['3rd Lowest Wk'];
        }

        $datum['IFPA Points'] = $datum['Total'] - $datum['Surplus MP'] + $datum['Attendance Bonus'];

        $su = SeasonUser::find()->where(['user_id' => $key, 'season_id' => $season_id])->one();
        if ($su == null) {
          $datum['Dues Paid?'] = '(error)';
        } else {
          $datum['Dues Paid?'] = $su->dues_string;
        }
      }

      return $data;
    }

    public static function seasonArrayDataProvider($season_id) {
      $season = Season::find()->where(['id' => $season_id])->one();

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
            'IFPA Points' => ['asc' => ['5 Weeks?' => SORT_DESC, 'IFPA Points' => SORT_DESC, 'MPO' => SORT_DESC], 
                                     'desc' => ['5 Weeks?' => SORT_DESC, 'IFPA Points' => SORT_ASC, 'MPO' => SORT_ASC]],
            'Total' => ['asc' => ['Total' => SORT_DESC], 'desc' => ['Total' => SORT_ASC]],
            'Weeks Played' => ['asc' => ['Weeks Played' => SORT_DESC], 'desc' => ['Weeks Played' => SORT_ASC]],
            'Weeks Absent' => ['asc' => ['Weeks Absent' => SORT_DESC], 'desc' => ['Weeks Absent' => SORT_ASC]],
            'Effective Opponent Count' => ['asc' => ['Effective Opponent Count' => SORT_DESC], 'desc' => ['Effective Opponent Count' => SORT_ASC]],
            'Effective Matchpoints' => ['asc' => ['Effective Matchpoints' => SORT_DESC], 'desc' => ['Effective Matchpoints' => SORT_ASC]],
            '4 Weeks?' => ['asc' => ['4 Weeks?' => SORT_DESC], 'desc' => ['4 Weeks?' => SORT_ASC]],
            '5 Weeks?' => ['asc' => ['5 Weeks?' => SORT_DESC], 'desc' => ['5 Weeks?' => SORT_ASC]],
            'Opponent Count' => ['asc' => ['Opponent Count' => SORT_DESC], 'desc' => ['Opponent Count' => SORT_ASC]],
            'Forfeit Opponent Count' => ['asc' => ['Forfeit Opponent Count' => SORT_DESC], 'desc' => ['Forfeit Opponent Count' => SORT_ASC]],
            'Dues Paid?' => ['asc' => ['Dues Paid?' => SORT_DESC, 'Weeks Played' => SORT_DESC], 
                             'desc' => ['Dues Paid?' => SORT_ASC, 'Weeks Played' => SORT_ASC]],
            //'MPO' => ['asc' => ['MPO' => SORT_DESC], 'desc' => ['MPO' => SORT_ASC]],
            'Adj. MPO' => ['asc' => ['5 Weeks?' => SORT_DESC, 'Adj. MPO' => SORT_DESC], 
                      'desc' => ['5 Weeks?' => SORT_DESC, 'Adj. MPO' => SORT_ASC]],
            'MPO' => ['asc' => ['5 Weeks?' => SORT_DESC, 'MPO' => SORT_DESC], 
                      'desc' => ['5 Weeks?' => SORT_DESC, 'MPO' => SORT_ASC]],
          ],
          'defaultOrder' => ['MPO' => SORT_ASC],
        ],
      ];

      for ($wn = 1; $wn <= $season->regular_season_length; ++$wn) {
        $adpinit['sort']['attributes']["Week $wn"] = ['asc' => ["Week $wn points" => SORT_DESC], 'desc' => ["Week $wn points" => SORT_ASC]];
        $adpinit['sort']['attributes']["Week $wn group"] = ['asc' => ["Week $wn groupnum" => SORT_ASC], 'desc' => ["Week $wn groupnum" => SORT_DESC]];
      }

      $provider = new ArrayDataProvider($adpinit);
      return $provider;
    }
}
