<?php

namespace app\controllers;

use Yii;
use app\models\Playermachinestats;
use app\models\PlayermachinestatsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminPlayermachinestatsController implements the CRUD actions for Playermachinestats model.
 */
class AdminPlayermachinestatsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Playermachinestats models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlayermachinestatsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Playermachinestats model.
     * @param integer $user_id
     * @param integer $machine_id
     * @return mixed
     */
    public function actionView($user_id, $machine_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($user_id, $machine_id),
        ]);
    }

    /**
     * Creates a new Playermachinestats model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Playermachinestats();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'user_id' => $model->user_id, 'machine_id' => $model->machine_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Playermachinestats model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $user_id
     * @param integer $machine_id
     * @return mixed
     */
    public function actionUpdate($user_id, $machine_id)
    {
        $model = $this->findModel($user_id, $machine_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'user_id' => $model->user_id, 'machine_id' => $model->machine_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Playermachinestats model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $user_id
     * @param integer $machine_id
     * @return mixed
     */
    public function actionDelete($user_id, $machine_id)
    {
        $this->findModel($user_id, $machine_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Playermachinestats model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $user_id
     * @param integer $machine_id
     * @return Playermachinestats the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($user_id, $machine_id)
    {
        if (($model = Playermachinestats::findOne(['user_id' => $user_id, 'machine_id' => $machine_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
