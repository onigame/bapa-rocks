<?php

namespace app\controllers;

use Yii;
use app\models\Poll;
use app\models\PollSearch;
use app\models\PollEligibility;
use app\models\PollChoice;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PollController implements the CRUD actions for Poll model.
 */
class PollController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all Poll models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PollSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Poll model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Poll model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Poll();

        $postdata = Yii::$app->request->post();
        
        if (array_key_exists('Poll', $postdata) && array_key_exists('pollEligibilities', $postdata['Poll'])) {
          $pollEligibilities = $postdata['Poll']['pollEligibilities'];
#          if ($pollEligibilities != null) {
#            throw new \yii\base\UserException(json_encode($pollEligibilities));
#          }
          unset($postdata['Poll']['pollEligibilities']);
#          throw new \yii\base\UserException(json_encode($postdata));
        } else {
          $pollEligibilities = array();
        }


        if ($model->load($postdata) && $model->save()) {

          foreach ($pollEligibilities as $season_id) {
            $pe = new Polleligibility();
            $pe->season_id = $season_id;
            $pe->poll_id = $model->id;
            $pe->save();
          }

          $dates = explode(",", $model->dates);
#          throw new \yii\base\UserException($model->dates);
          foreach ($dates as $date) {
            $pc = new Pollchoice();
            $pc->name = $date;
            $pc->poll_id = $model->id;
            $pc->save();
          }

          return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Poll model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Poll model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Poll model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Poll the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Poll::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
