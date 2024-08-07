<?php

namespace app\controllers;

use Yii;
use app\models\Season;
use app\models\SeasonUser;
use app\models\Session;
use app\models\SessionUser;
use app\models\PublicSeasonSearch;
use app\models\PublicSeasonUserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * SeasonController implements views for seasons.
 */
class SeasonController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['view', 'view2', 'update', 'create', 'delete', 'create-playoffs', 'create-session'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'create', 'create-playoffs', 'create-session'],
                        'roles' => ['Manager'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'view2'],
                        'roles' => ['@','?'],
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
     * Lists all Season models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PublicSeasonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a regular session
     * @param integer $season_id
     * @return mixed
     */
    public function actionCreateSession($season_id) {
      $season = Season::findOne($season_id);

      $proposednum = count($season->sessions) + 1;

      $session = new Session();
      $session->season_id = $season_id;
      $session->name = "Week ".$proposednum;
      $session->type = 1;
      $session->status = 0;
      $session->location_id = $season->lastLocationId;

      if ($session->load(Yii::$app->request->post()) && $session->save()) {
        return $this->redirect(['/session/view', 'id' => $session->id]);
      } else {
        return $this->render('create_session', [
          'model' => $session,
        ]);
      }

    }

    /**
     * Displays the "make playoffs" page.
     * @param integer $season_id
     * @return mixed
     */
    public function actionCreatePlayoffs($season_id) {
        $season = Season::findOne($season_id);
        $newPlayoffsModel = new Session(); // going to be for playoffs
        $searchModel = new PublicSeasonUserSearch();
        $dataProvider = $searchModel->search(['season_id' => $season->id]);

        $newPlayoffsModel->type = 2;
        $newPlayoffsModel->status = 0;
        $newPlayoffsModel->season_id = $season->id;

        if ($newPlayoffsModel->load(Yii::$app->request->post())) {
 
          $season::getDb()->transaction(function($db) use ($season, $newPlayoffsModel) {
            if (!$newPlayoffsModel->save()) {
              Yii::error($newPlayoffsModel->errors);
              throw new \yii\base\UserException("Error saving session (new playoffs) :"
                  . json_encode($newPlayoffsModel->getErrors()));
            }
            $seasonuserids = \yii\helpers\Json::decode($newPlayoffsModel->playoffdata);
            // make sessionuser for all of these.
          
            foreach ($seasonuserids as $seasonuserid) {
              $newSessionUser = new SessionUser();
              $newSessionUser->user_id = SeasonUser::findOne($seasonuserid)->user_id;
              $newSessionUser->session_id = $newPlayoffsModel->id;
              $newSessionUser->status = 1;
              $newSessionUser->recorder_id = Yii::$app->user->id;
              if (!$newSessionUser->save()) {
                Yii::error($newSessionUser->errors);
                throw new \yii\base\UserException("Error saving sessionUser");
              }
            }
            return $this->redirect(['/session/view', 'id' => $newPlayoffsModel->id]);
          });
        }

        $seasonModel = $season;
        $newPlayoffsModel->name = "Playoffs replacemewithdivisionname";
        return $this->render('createplayoffs', [
          'newplayoffs' => $newPlayoffsModel,
          'season' => $season,
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFinish($id) {
      $season = $this->findModel($id);
      $season->status = 3;
      $season->save();

      $searchModel = new PublicSeasonSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

      return $this->render('index', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
      ]);
    }


    /**
     * Displays a single Season model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single Season model.
     * @param integer $id
     * @return mixed
     */
    public function actionView2($id, $id2)
    {
        return $this->render('view2', [
            'model' => $this->findModel($id),
            'model2' => $this->findModel($id2),
        ]);
    }

    /**
     * Creates a new Season model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Season();
        $model->status = 1; // all new seasons start out as started for now.

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Season model.
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
     * Deletes an existing Season model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Season model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Season the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Season::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
