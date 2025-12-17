<?php

namespace app\controllers;

use Yii;
use app\models\Match;
use app\models\MatchSearch;
use app\models\Game;
use app\models\GameSearch;
use app\models\MatchUser;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * AdminMatchController implements the CRUD actions for Match model.
 */
class MatchController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'create', 'delete', 'deleterecurse'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['deleterecurse'],
                        'roles' => ['GenericAdminPermission'],
                    ],
                    [
                        'allow' => false,
                        'actions' => ['update', 'create', 'delete', 'deleterecurse'],
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
     * Lists all Match models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MatchSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Match model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
      $model = $this->findModel($id);
      if ($model->statusDetailCode == 0) {
        return $this->render('view_waiting', [
            'model' => $this->findModel($id),
        ]);
      }
      if ($model->statusDetailCode == 1) {
        return $this->render('view_ready', [
            'model' => $this->findModel($id),
        ]);
      }
      if ($model->statusDetailCode == 2) {
        return $this->render('view_broken', [
            'model' => $this->findModel($id),
        ]);
      }
      if ($model->statusDetailCode == 3) {
        return $this->render('view_completed', [
            'model' => $this->findModel($id),
        ]);
      }

      return $this->render('view_inprogress', [
        'model' => $this->findModel($id),
      ]);
    }

    /**
     * Creates a new Match model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Match();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Match model.
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
     * Deletes an existing Match model.
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
     * Recursively deletes a match and its children (games, scores).
     * @param int $id Match ID
     */
    public function actionDeleterecurse($id)
    {
      $match = $this->findModel($id);
      $session = $match->session;
      $match::getDb()->transaction(function($db) use ($match) {
        $match->deleteChildren();
        $match->delete();
      });

      return $this->redirect(['/session/view', 'id' => $session->id]);
    }


    /* Does the appropriate thing on the match,
     * for example, creates games if needed.
     */
    /**
     * Triggers the next automated step for a match.
     * E.g. creating games if they don't exist.
     * @param int $id Match ID
     */
    public function actionGo($id) {
      $match = $this->findModel($id);
      $match->maybeStartMatch();
      return $this->redirect(['view', 'id' => $match->id]);
    }

    /**
     * Finds the Match model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Match the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Match::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
