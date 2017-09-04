<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Security;
use app\models\SeasonUser;
use yii\helpers\Html;

/**
 * This is the model class for table "seasonuser", with public enhancements.
 *
 * @property integer $id
 * @property string $notes
 * @property integer $matchpoints
 * @property integer $game_count
 * @property integer $opponent_count
 * @property integer $match_count
 * @property integer $dues
 * @property integer $user_id
 * @property integer $season_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $playoff_division
 * @property integer $playoff_rank
 *
 * @property integer $row_number
 * @property double $mpg
 *
 * @property User $user
 * @property Season $season
 */
class PublicSeasonUser extends SeasonUser
{
    public $row_number = null;
    private $tiebreaker = 0;  // random string

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seasonuser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'matchpoints', 'game_count', 'opponent_count', 
              'match_count', 'dues', 'mpg',
              'user_id', 'season_id'], 'required'],
            [['matchpoints', 'game_count', 'opponent_count', 'match_count', 'dues', 'playoff_rank', 'user_id', 
              'season_id', 'created_at', 'updated_at', 'row_number'], 'integer'],
            [['mpg'], 'double'],
            [['notes'], 'string', 'max' => 255],
            [['playoff_division'], 'string', 'max' => 20],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season::className(), 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        return $labels;
    }

    public function getRecommended_Division() {
      if ($this->dues != 1 or $this->match_count < 5) {
        return "No";
      } else if ($this->row_number <= 12) {
        return "A";
      } else {
        return "B";
      }
    }

    public function getTiebreaker() {
      return Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * @inheritdoc
     * @return SeasonUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PublicSeasonUserQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'bedezign\yii2\audit\AuditTrailBehavior',
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }
}
