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
use app\models\Eliminationgraph;
use app\models\Match;
use app\models\MatchUser;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
                'only' => ['update', 'create', 'delete', 'start', 'removeplayer', 'addplayer', 'finish', 'leave', 'join'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'create', 'start', 'removeplayer', 'addplayer', 'finish'],
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
          return $this->render('view_notstarted', [
            'model' => $model,
            'indataProvider' => $indataProvider,
            'outdataProvider' => $outdataProvider,
          ]);
        }
        if ($model->status == 1) {
          $searchModel = new MatchSearch();
          $searchModel->session_id = $id;
          $dataProvider = $searchModel->search([]);
          return $this->render('view_inprogress', [
            'model' => $model,
            'matchSearchModel' => $searchModel,
            'matchDataProvider' => $dataProvider,
          ]);
        }
        if ($model->status == 2) {
          $searchModel = new MatchSearch();
          $dataProvider = $searchModel->search(['session_id' => $id]);
          return $this->render('view_completed', [
            'model' => $model,
            'matchSearchModel' => $searchModel,
            'matchDataProvider' => $dataProvider,
          ]);
        }
    }

    public function actionFinish($id) {
      $session = $this->findModel($id);
      $session->status = 2;
      $session->save();
      return $this->redirect(['view', 'id' => $id]);
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
      //return $this->redirect(['view', 'id' => $session_id]);
    }

    public function actionRemoveplayer($session_id, $seasonuser_id) {
      $sessionUser = SessionUser::find()->where(['session_id' => $session_id,
                                                 'user_id' => PublicSeasonUser::findOne($seasonuser_id)->user_id])
                                        ->one();
      if ($sessionUser == null) {
        throw new \yii\base\UserException("That player is already not playing!");
      }
      $dcount = $sessionUser->delete();
      if ($dcount != 1) {
        Yii::error($dcount);
        throw new \yii\base\UserException("Error deleting sessionUser");
      }
      $this->actionView($session_id);
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
        Yii::error("Starting regular season is Not implemented yet!");
        Yii::$app->session->setFlash('error', "Starting regular season is Not implemented yet!");
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

    public function makePlayoffMatches($id) {
      $session = $this->findModel($id);
      $sessionUsers = SessionUser::find()->where(['session_id' => $id])->all();

      // sort all the players by their initial seed.
      usort ($sessionUsers, function($a, $b) {
        if ($a->seasonmpg > $b->seasonmpg) return -1;
        if ($a->seasonmpg < $b->seasonmpg) return 1;
        if ($a->seasonMatchpoints > $b->seasonMatchpoints) return -1;
        if ($a->seasonMatchpoints < $b->seasonMatchpoints) return 1;
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
