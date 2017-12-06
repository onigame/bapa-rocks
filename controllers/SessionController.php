<?php

namespace app\controllers;

use Yii;
use app\models\Session;
use app\models\SessionSearch;
use app\models\SessionUser;
use app\models\SeasonUser;
use app\models\MatchSearch;
use app\models\PublicSeasonUserSearch;
use app\models\PublicSeasonUser;
use app\models\Regularmatchpoints;
use app\models\Eliminationgraph;
use app\models\Match;
use app\models\MatchUser;
use app\models\Player;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * SessionController implements the CRUD actions for Session model.
 */
class SessionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'create', 'delete', 'start', 'removeplayer', 'addplayer', 'finish', 'leave', 'join', 'addlate'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'create', 'start', 'removeplayer', 'addplayer', 'finish', 'addlate'],
                        'roles' => ['Manager'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['leave', 'join'],
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Session models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SessionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function otherdataProvider($id) {
      $query = Player::find();
      $dataProvider = new ActiveDataProvider([
        'query' => $query,
        'sort' => [
          'defaultOrder' => ['name' => SORT_ASC],
          'attributes' => [
             'id',
             'name' => [
                'asc' => ['profile.name' => SORT_ASC],
                'desc' => ['profile.name' => SORT_DESC],
                'label' => 'Player Name',
             ],
          ]
        ],
        'pagination' => [
          'pageSize' => 0,
        ],

      ]);

      $query->select(['id', 'profile.name']);

      $query->from(['user']);

      $query->joinWith(['profile']);

      return $dataProvider;
    }

    /**
     * Displays a single Session model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model->status == 0) {
          $searchModel = new PublicSeasonUserSearch();
          $indataProvider = $searchModel->search(['season_id' => $model->season_id,
                                                'session_id' => $id,
                                                'include' => 'in',
                                               ]);
          $outdataProvider = $searchModel->search(['season_id' => $model->season_id,
                                                'session_id' => $id,
                                                'include' => 'not in',
                                               ]);
          $otherdataProvider = $this->otherdataProvider($id);
          return $this->render('view_notstarted', [
            'model' => $model,
            'indataProvider' => $indataProvider,
            'outdataProvider' => $outdataProvider,
            'otherdataProvider' => $otherdataProvider,
          ]);
        }
        if ($model->status == 1 && $model->type == 2) {
          $searchModel = new MatchSearch();
          $searchModel->session_id = $id;
          $dataProvider = $searchModel->search([]);
          return $this->render('view_playoffinprogress', [
            'model' => $model,
            'matchSearchModel' => $searchModel,
            'matchDataProvider' => $dataProvider,
          ]);
        }
        if ($model->status == 1 && $model->type == 1) {
          $searchModel = new MatchSearch();
          $searchModel->session_id = $id;
          $dataProvider = $searchModel->search([]);
          return $this->render('view_regularinprogress', [
            'model' => $model,
            'matchSearchModel' => $searchModel,
            'matchDataProvider' => $dataProvider,
          ]);
        }
        if ($model->status == 2 && $model->type == 2) {
          $searchModel = new MatchSearch();
          $dataProvider = $searchModel->search(['session_id' => $id]);
          return $this->render('view_playoffcompleted', [
            'model' => $model,
            'matchSearchModel' => $searchModel,
            'matchDataProvider' => $dataProvider,
          ]);
        }
        if ($model->status == 2 && $model->type == 1) {
          $searchModel = new MatchSearch();
          $dataProvider = $searchModel->search(['session_id' => $id]);
          return $this->render('view_regularcompleted', [
            'model' => $model,
            'matchSearchModel' => $searchModel,
            'matchDataProvider' => $dataProvider,
          ]);
        }
    }

    /**
     * Displays the "add late" screen.
     * @param integer $id
     * @return mixed
     */
    public function actionAddlate($id)
    {
        $model = $this->findModel($id);
        if ($model->status == 1 && $model->type == 1) {

          $openMatches = [];
          foreach ($model->matches as $match) {
            if ($match->latePlayerOkay) $openMatches[] = $match;
          }

          $searchModel = new PublicSeasonUserSearch();

          $data = [];
          $seasonusers = SeasonUser::find()->from(['seasonuser s'])->where(['season_id' => $model->season_id])
             ->andWhere(['not in', 's.user_id', SessionUser::find()->select('user_id')
                                               ->where(['session_id' => $id])])->all();
          foreach ($seasonusers as $su) {
            $data[$su->id]['id'] = $su->id;
            $data[$su->id]['Name'] = $su->playerName;

            $buttons = "";
            foreach ($openMatches as $match) {
              $buttons .= Html::a(
                    $match->code,
                    [ 'addlateplayer',
                      'id' => $id,
                      'match_id' => $match->id,
                      'user_id' => $su->user->id,
                    ],
                    [
                      'title' => 'Add Late',
                      'class' => 'btn-sm btn-success',
                    ]
                  );
              $buttons .= " &nbsp; ";
            }
            $data[$su->id]['Join'] = $buttons;
          }
          $arrayData = ArrayHelper::toArray($data);
          $adpinit = [
            'allModels' => $arrayData,
            'pagination' => [
              'pageSize' => 0,
            ],
            'sort' => [
              'attributes' => [
                'Name',
                'id',
              ],
            ],
          ];
          $outdataProvider = new ArrayDataProvider($adpinit);
/*
          $outdataProvider = $searchModel->search(['season_id' => $model->season_id,
                                                'session_id' => $id,
                                                'include' => 'not in',
                                               ]);
*/

          $searchModel = new MatchSearch();
          $searchModel->session_id = $id;
          $dataProvider = $searchModel->search([]);
          return $this->render('addlate', [
            'model' => $model,
            'matchSearchModel' => $searchModel,
            'matchDataProvider' => $dataProvider,
            'outdataProvider' => $outdataProvider,
          ]);
        }
    }

    public function actionAddlateplayer($id, $match_id, $user_id) {
      $session = Session::findOne($id);
      $match = Match::findOne($match_id);
      $player = Player::findOne($user_id);

      $session->addLatePlayer($user_id);
      $match->addRegularPlayer($user_id);

      Yii::$app->session->setFlash('success', "Added ".$player->name." as latecomer to ".$match->code." successfully!");
      return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionFinish($id) {
      $session = $this->findModel($id);
      if ($session->type == 1) {
        $session::getDb()->transaction(function($db) use ($session) {

          $scoresData = Regularmatchpoints::rawData($session->season->id);

          foreach ($session->matchUsers as $mu) {
            $su = SeasonUser::find()->where(['season_id' => $session->season_id, 'user_id' => $mu->user_id])->one();
            $su->matchpoints += $mu->matchpoints;
            $su->game_count += $mu->game_count;
            $su->opponent_count += $mu->opponent_count;
            $su->forfeit_opponent_count += $mu->forfeit_opponent_count;
            $su->match_count += 1;

            $su->surplus_matchpoints = $scoresData[$mu->user_id]['Surplus MP'];
            $su->surplus_mpo_matchpoints = $scoresData[$mu->user_id]['Surplus MPO EM'];
            $su->surplus_mpo_opponent_count = $scoresData[$mu->user_id]['Surplus MPO EO'];

            if (!$su->save()) {
              Yii::error($su->errors);
              throw new \yii\base\UserException("Error saving su in actionFinish");
            }
          }

          $session->status = 2;
          if (!$session->save()) {
            Yii::error($session->errors);
            throw new \yii\base\UserException("Error saving session in actionFinish");
          }
        });

      } else if ($session->type == 2) {
        $session->status = 2;
        $session->save();
      } else {
        throw new \yii\base\UserException("Unexpected session type in actionFinish!");
      }
      return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAddotherplayer($session_id, $user_id) {
      $sessionUser = SessionUser::find()->where(['session_id' => $session_id,
                                                 'user_id' => $user_id])
                                        ->one();
      if ($sessionUser != null) {
        throw new \yii\base\UserException("That player is already playing!");
      }

      $session = Session::findOne($session_id);

      $seasonUser = SeasonUser::find()->where(['season_id' => $session->season->id,
                                                'user_id' => $user_id])
                                       ->one();
      if ($seasonUser == null) {
        $session->season->addplayer($user_id);
        $seasonUser = SeasonUser::find()->where(['season_id' => $session->season->id,
                                                  'user_id' => $user_id])
                                         ->one();
        if ($seasonUser == null) {
          throw new \yii\base\UserException("Creation of SeasonUser failed somehow");
        }
      }

      $session->addPlayer($seasonUser);
      return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionJoin($id) {
      $player_id = Yii::$app->user->id;
      $sessionUser = SessionUser::find()->where(['session_id' => $id,
                                                 'user_id' => $player_id])
                                        ->one();
      if ($sessionUser != null) {
        throw new \yii\base\UserException("You are already playing!");
      }

      $session = Session::findOne($id);
      $seasonUser = SeasonUser::find()->where(['season_id' => $session->season->id,
                                                'user_id' => $player_id])
                                       ->one();
      if ($seasonUser == null) {
        $session->season->addplayer($player_id);
        $seasonUser = SeasonUser::find()->where(['season_id' => $session->season->id,
                                                  'user_id' => $player_id])
                                         ->one();
        if ($seasonUser == null) {
          throw new \yii\base\UserException("Creation of SeasonUser failed somehow");
        }
      }
      $session->addPlayer($seasonUser);
      return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionLeave($id) {
      $player_id = Yii::$app->user->id;
      $sessionUser = SessionUser::find()->where(['session_id' => $id,
                                                 'user_id' => $player_id])
                                        ->one();
      if ($sessionUser == null) {
        throw new \yii\base\UserException("You can't leave; you're not in this session!");
      }
      $sessionUser->delete();

      $session = Session::findOne($id);
      $seasonUser = SeasonUser::find()->where(['season_id' => $session->season->id,
                                                'user_id' => $player_id])
                                       ->one();
      if (count($seasonUser->sessionUsers) == 0) { // this was the user's last session
        $seasonUser->delete();
      }
      return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAddplayer($session_id, $seasonuser_id) {
      $sessionUser = SessionUser::find()->where(['session_id' => $session_id,
                                                 'user_id' => PublicSeasonUser::findOne($seasonuser_id)->user_id])
                                        ->one();
      if ($sessionUser != null) {
        throw new \yii\base\UserException("That player is already playing!");
      }

      $session = Session::findOne($session_id);
      $session->addPlayer(SeasonUser::findOne($seasonuser_id));

      $this->actionView($session_id);
      return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionRemoveplayer($session_id, $seasonuser_id) {
      $user_id = PublicSeasonUser::findOne($seasonuser_id)->user_id;
      $sessionUser = SessionUser::find()->where(['session_id' => $session_id,
                                                 'user_id' => $user_id])
                                        ->one();
      if ($sessionUser == null) {
        throw new \yii\base\UserException("That player is already not playing!");
      }
      $season_id = $sessionUser->session->season_id;
      $dcount = $sessionUser->delete();
      if ($dcount != 1) {
        Yii::error($dcount);
        throw new \yii\base\UserException("Error deleting sessionUser");
      }

      $seasonUser = SeasonUser::find()->where(['season_id' => $season_id,
                                                'user_id' => $user_id])
                                       ->one();
      if (count($seasonUser->sessionUsers) == 0) { // this was the user's last session
        $seasonUser->delete();
      }

      $this->actionView($session_id);
      return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Creates a new Session model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Session();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Session model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Session model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionStart($id) {
      $session = $this->findModel($id);
      if ($session->type == 1 ) {
        $count = SessionUser::find()->where(['session_id' => $id])->count();
        if ($count < 6) {
          Yii::$app->session->setFlash('error', "Need at least 6 players to start regular season week!");
          return $this->redirect(['view', 'id' => $id]);
        }
        $this->makeRegularMatches($id);
        return $this->redirect(['view', 'id' => $id]);
      }
      if ($session->type == 2 ) {
        $count = SessionUser::find()->where(['session_id' => $id])->count();
        if ($count < 2) {
          Yii::$app->session->setFlash('error', "Need at least 2 players to start playoff!");
          return $this->redirect(['view', 'id' => $id]);
        }
        $this->makePlayoffMatches($id);
        return $this->redirect(['view', 'id' => $id]);
      }
      Yii::$app->session->setFlash('error', "Unrecognized Session Type: " . $session->type);
      return $this->redirect(['view', 'id' => $id]);
    }

    public static function matchCount($numplayers) {
      if ($numplayers <= 8) return 2;
      if ($numplayers <= 11) return 3;
      return floor(($numplayers+5)/4);
    }

    public static function groupSize($numplayers, $groupnum) {
      if ($groupnum == 1 && $numplayers == 8) return 4;
      if ($groupnum == 1) return 3;
      if ($groupnum == 2 && $numplayers <= 8) return 4;
      if ($groupnum == 2 && $numplayers == 11) return 4;
      if ($groupnum == 2) return 3;
      if ($groupnum == 3 && $numplayers == 10) return 4;
      if ($groupnum == 3 && $numplayers == 11) return 4;
      if ($groupnum == 3 && ($numplayers % 4) == 2) return 4;
      if ($groupnum == 3) return 3;
      if ($groupnum == 4 && ($numplayers % 4) == 1) return 4;
      if ($groupnum == 4 && ($numplayers % 4) == 2) return 4;
      if ($groupnum == 4) return 3;
      if ($groupnum == 5 && ($numplayers % 4) == 3) return 3;
      //if ($groupnum == 5) return 4;
      return 4;
    }

    public static function matchNumber($numplayers, $playernum) {
      $groupnum = 0;
      $headCount = 0;
      while ($playernum > $headCount) {
        $groupnum++;
        $headCount += SessionController::groupSize($numplayers, $groupnum);
      }
      return $groupnum;
    }

    public function makeRegularMatches($id) {
      $session = $this->findModel($id);
      $sessionUsers = SessionUser::find()->where(['session_id' => $id])->all();

      // sort all the players by their previous performance.
      usort ($sessionUsers, ['app\models\SessionUser', 'byPreviousPerformance']);

      $session::getDb()->transaction(function($db) use ($session, $sessionUsers) {
        $numplayers = count($sessionUsers);
        $matchcount = SessionController::matchCount($numplayers);
        $matches = [];
        for ($m = 1; $m <= $matchcount; ++$m) {
          $match = new Match();
          $match->session_id = $session->id;
          $match->code = "Group " . $m;
          $gs = SessionController::groupSize($numplayers, $m);
          if ($gs == 4) {
            $match->format = 4; 
          } else if ($gs == 3) {
            $match->format = 3;
          } else {
            throw new \yii\base\UserException("Weird group size");
          }
          $match->status = 0;
          if (!$match->save()) {
            Yii::error($match->errors);
            throw new \yii\base\UserException("Error saving match");
          }
          $matches[] = $match;
        }

        // assign players to their initial match
        $playernum = 1;
        foreach ($sessionUsers as $sessionuser) {
          $matches[SessionController::matchNumber($numplayers, $playernum)-1]
            ->addRegularPlayer($sessionuser->user_id);
          $playernum++;
        }

        $session->status = 1;
        if (!$session->save()) {
          Yii::error($session->errors);
          throw new \yii\base\UserException("Error saving session when seed = " . $seed);
        }
      });
    }

    public function makePlayoffMatches($id) {
      $session = $this->findModel($id);
      $sessionUsers = SessionUser::find()->where(['session_id' => $id])->all();

      // sort all the players by their initial seed.
      usort ($sessionUsers, function($a, $b) {
        if ($a->seasonUser->mpo > $b->seasonUser->mpo) return -1;
        if ($a->seasonUser->mpo < $b->seasonUser->mpo) return 1;
        if ($a->seasonUser->matchpoints > $b->seasonUser->matchpoints) return -1;
        if ($a->seasonUser->matchpoints < $b->seasonUser->matchpoints) return 1;
        if ($a->tiebreaker > $b->tiebreaker) return -1;
        if ($a->tiebreaker < $b->tiebreaker) return 1;
        return 0;
      });

      $session::getDb()->transaction(function($db) use ($session, $sessionUsers) {
        $numplayers = count($sessionUsers);
  
        // create all the matches.
        $eliminationgraphmatches = Eliminationgraph::find()
          ->where(['and', 'seed_p1<'.$numplayers, 'seed_p2<'.$numplayers])
          ->all();
  
        foreach ($eliminationgraphmatches as $egmatch) {
          $match = new Match();
          $match->session_id = $session->id;
          $match->code = $egmatch->code;
          if ($egmatch->bracket === "S") {
            $match->format = 7;  // Championship is best of 7
          } else if ($egmatch->bracket === "W") {
            $match->format = 5;  // Winner's is best of 5
          } else {
            $match->format = 1;  // Other is best of 3
          }
          $match->status = 0;
          if (!$match->save()) {
            Yii::error($match->errors);
            throw new \yii\base\UserException("Error saving match");
          }
        }
  
        // assign players to their initial match (by creating the MatchUser models)
        $seed = 0;
        foreach ($sessionUsers as $sessionuser) {
          $graph = Eliminationgraph::firstMatchForSeed($seed, $numplayers);
          $match = Match::find()->where(['session_id' => $session->id,
                                'code' => $graph->code])->one();
          $match->addPlayoffPlayer($graph->code, $sessionuser->user_id, $seed);
          // add playoff division to season user
          $seasonuser = $sessionuser->publicSeasonUser;
          $seasonuser->playoff_division = $session->playoff_division;
          if (!$seasonuser->save()) {
            Yii::error($seasonuser->errors);
            throw new \yii\base\UserException("Error saving seasonuser when seed = " . $seed);
          }
          $seed++;
        }

        $session->status = 1;
        if (!$session->save()) {
          Yii::error($session->errors);
          throw new \yii\base\UserException("Error saving session when seed = " . $seed);
        }
      });
    }

    /**
     * Finds the Session model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Session the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Session::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
