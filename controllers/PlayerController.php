<?php

namespace app\controllers;

use Yii;
use app\models\Player;
use app\models\Machine;
use app\models\Score;
use app\models\Playermachinestats;
use app\models\PlayermachinestatsSearch;
use app\models\PlayerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * PlayerController implements the CRUD actions for Player model.
 */
class PlayerController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'recomputestats', 'vaccstatuschange'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['recomputestats', 'vaccstatuschange'],
                        'roles' => ['Manager'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'actions' => ['vaccstatuschange'],
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
     * Lists all Player models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlayerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
  
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Player model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $statsSearchModel = new PlayermachinestatsSearch();
        $statsDataProvider = $statsSearchModel->search(Yii::$app->request->queryParams);
        $statsDataProvider->query->andWhere(['user_id' => $id]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'statsSearchModel' => $statsSearchModel,
            'statsDataProvider' => $statsDataProvider,
        ]);
    }

    /**
     * Creates a new Player model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Player();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Player model.
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
     * Deletes an existing Player model.
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
     * Changes Vaccine status
     */
    public function actionVaccstatuschange($id, $vaccstatus) {
      $player = $this->findModel($id);
      $model = $player->profile;
      $model->vaccination = $vaccstatus;
      if (!$model->save()) {
        Yii::error($model->errors);
        throw new \yii\base\UserException("Error saving model at actionVaccstatuschange");
      }
      return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Recomputes stats for all players.
     */
    public function actionRecomputestats() {
        Playermachinestats::deleteAll();

        $players = Player::find()->all();
        foreach ($players as $player) {
          $machines = Machine::find()->all();
          foreach ($machines as $machine) {
            Playermachinestats::recomputeStatsSingle($player->id, $machine->id);
          }
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Player model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Player the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Player::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
