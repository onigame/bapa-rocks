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
                'only' => ['index', 'view', 'recomputestats'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['recomputestats'],
                        'roles' => ['Manager'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
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
     * Recomputes stats for all players.
     */
    public function actionRecomputestats() {
        Playermachinestats::deleteAll();

        $players = Player::find()->all();
        foreach ($players as $player) {
          $machines = Machine::find()->all();
          foreach ($machines as $machine) {
            $this->actionRecomputesingle($player->id, $machine->id);
          }
        }

        return $this->redirect(['index']);
    }

    /**
     * Recomputes stats for one player / machine
     */
    public function actionRecomputesingle($id, $machine_id) {

      $playermachinestats = new Playermachinestats();
      $playermachinestats->user_id = $id;
      $playermachinestats->machine_id = $machine_id;
      
      $playermachinestats->forfeitcount = Score::find()
                ->leftJoin('game', 'game.id = score.game_id')
                ->where(['user_id' => $id,
                         'game.machine_id' => $machine_id,
                         'forfeit' => 1,
                         'game.status' => 4, // only completed games count.
                        ])
                ->orderBy('forfeit DESC, value')
                ->count();

      $scores = Score::find()
                ->leftJoin('game', 'game.id = score.game_id')
                ->where(['user_id' => $id,
                         'game.machine_id' => $machine_id,
                         'forfeit' => 0,
                         'game.status' => 4, // only completed games count.
                        ])
                ->orderBy('forfeit DESC, value')
                ->all();

      $playermachinestats->nonforfeitcount = 0;
      $playermachinestats->totalmatchpoints = 0;
      $scorelist = [];

      $has_score = false;
      foreach ($scores as $score) {
        $playermachinestats->nonforfeitcount++;
        $scorelist[] = $score->value;
        if ($has_score) {
          if ($score->value > $playermachinestats->scoremax) {
            $playermachinestats->scoremax = $score->value;
            $playermachinestats->scoremaxgame_id = $score->game_id;
          } else if ($score->value < $playermachinestats->scoremin) {
            $playermachinestats->scoremin = $score->value;
            $playermachinestats->scoremingame_id = $score->game_id;
          }
        } else {
          $has_score = true;
          $playermachinestats->scoremax = $score->value;
          $playermachinestats->scoremaxgame_id = $score->game_id;
          $playermachinestats->scoremin = $score->value;
          $playermachinestats->scoremingame_id = $score->game_id;
        }
        $playermachinestats->totalmatchpoints += $score->matchpoints;
      }

      if ($has_score) {
        if ($playermachinestats->nonforfeitcount % 2 == 1) {
          $playermachinestats->scoremedian = $scorelist[($playermachinestats->nonforfeitcount - 1) / 2];
        } else {
          $s1 = $scorelist[($playermachinestats->nonforfeitcount - 2) / 2];
          $s2 = $scorelist[($playermachinestats->nonforfeitcount) / 2];
          $playermachinestats->scoremedian = floor(($s1 + $s2)/2);
        }
      }

      if ($has_score || $playermachinestats->forfeitcount > 0) {
        $playermachinestats->save();
      }
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
