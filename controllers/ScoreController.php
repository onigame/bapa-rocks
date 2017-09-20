<?php

namespace app\controllers;

use Yii;
use app\models\Score;
use app\models\ScoreSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * AdminScoreController implements the CRUD actions for Score model.
 */
class ScoreController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'create', 'delete', 'verify', 'forfeit', 'unforfeit'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [],
                        'roles' => ['GenericManagerPermission'],
                    ],
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['verify', 'forfeit', 'unforfeit'],
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
     * Lists all Score models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ScoreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Score model.
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
     * Creates a new Score model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Score();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Score model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($game->status != 3) {
            Yii::$app->session->setFlash('error', "Only scores on games in progress can be modified!");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Score model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionForfeit($id) {
      $score = $this->findModel($id);
      if ($score->game->status != 3) {
        Yii::$app->session->setFlash('warning', "only in-progress games can be forfeited!");
        throw new \yii\base\UserException("only in-progress games can be forfeited!");
      }
      if ($score->value == NULL && $score->forfeit == 1) {
        Yii::$app->session->setFlash('warning', "already forfeited!");
        throw new \yii\base\UserException("already forfeited!");
      }
      $score->forfeit = 1;
      $score->recorder_id = Yii::$app->user->id;
      $score->verified = 0;
      $score->verifier_id = null;
      $score->value = -1;
      $score->save();
      $score->game->maybeCompleted();
      return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUnforfeit($id) {
      $score = $this->findModel($id);
      if ($score->game->status != 3) {
        Yii::$app->session->setFlash('warning', "only in-progress games can be unforfeited!");
        throw new \yii\base\UserException("only in-progress games can be unforfeited!");
      }
      if ($score->value == NULL && $score->forfeit == 0) {
        Yii::$app->session->setFlash('warning', "already not forfeited!");
        throw new \yii\base\UserException("already not forfeited!");
      }
      $score->forfeit = 0;
      $score->recorder_id = Yii::$app->user->id;
      $score->verified = 0;
      $score->verifier_id = null;
      $score->value = NULL;
      $score->save();
      return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionVerify($id) {
      $score = $this->findModel($id);
      if ($score->value == NULL && $score->forfeit == 0) {
        Yii::$app->session->setFlash('warning', "Cannot verify empty score!");
        throw new \yii\base\UserException("Cannot verify empty score!");
      }
      if ($score->verifier_id != null && $score->verifier_id != $score->recorder_id) {
        $message = "Score is already verified!";
        Yii::$app->session->setFlash('warning', $message);
        throw new \yii\base\UserException($message);
      }
      $current = Yii::$app->user->id;
      $score->verifier_id = $current;
      $score->verified = 1;
      $score->save();
      $score->game->maybeCompleted();
      return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Score model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Score the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Score::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
