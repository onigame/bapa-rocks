<?php

namespace app\controllers;

use Yii;
use app\models\MachinePool;
use app\models\MachinePoolSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminMachinePoolController implements the CRUD actions for MachinePool model.
 */
class AdminMachinePoolController extends Controller
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
     * Lists all MachinePool models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MachinePoolSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MachinePool model.
     * @param integer $machine_id
     * @param integer $user_id
     * @param integer $session_id
     * @return mixed
     */
    public function actionView($machine_id, $user_id, $session_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($machine_id, $user_id, $session_id),
        ]);
    }

    /**
     * Creates a new MachinePool model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MachinePool();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'machine_id' => $model->machine_id, 'user_id' => $model->user_id, 'session_id' => $model->session_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MachinePool model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $machine_id
     * @param integer $user_id
     * @param integer $session_id
     * @return mixed
     */
    public function actionUpdate($machine_id, $user_id, $session_id)
    {
        $model = $this->findModel($machine_id, $user_id, $session_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'machine_id' => $model->machine_id, 'user_id' => $model->user_id, 'session_id' => $model->session_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MachinePool model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $machine_id
     * @param integer $user_id
     * @param integer $session_id
     * @return mixed
     */
    public function actionDelete($machine_id, $user_id, $session_id)
    {
        $this->findModel($machine_id, $user_id, $session_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MachinePool model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $machine_id
     * @param integer $user_id
     * @param integer $session_id
     * @return MachinePool the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($machine_id, $user_id, $session_id)
    {
        if (($model = MachinePool::findOne(['machine_id' => $machine_id, 'user_id' => $user_id, 'session_id' => $session_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
