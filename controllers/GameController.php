<?php

namespace app\controllers;

use Yii;
use app\models\Game;
use app\models\GameSearch;
use app\models\Machine;
use app\models\QueueGame;
use app\models\Score;
use app\models\MachineStatus;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AdminGameController implements the CRUD actions for Game model.
 */
class GameController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'create', 'delete', 'deleterecurse', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['kick'],
                        'roles' => ['GenericManagerPermission'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['deleterecurse'],
                        'roles' => ['GenericAdminPermission'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
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
     * Lists all Game models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GameSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    // should only be called when something is wrong
    public function actionKick($id) {
      $game = $this->findModel($id); 
      $game::getDb()->transaction(function($db) use ($game) {
        $game->deleteChildren();
        $game->machine_id = null;
        if ($game->isPlayoffs) {
          $game->status = 0;
        } else {
          $game->status = 6;
        }
        if (!$game->save()) {
          Yii::error($game->errors);
          throw new \yii\base\UserException("Error saving game direction actionKick" . $game->id);
        }
      });

      return $this->render('view', [
          'model' => $this->findModel($id),
      ]);

    }

    /**
     * Displays a single Game model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        // are we getting a score entry?
        // from: http://webtips.krajee.com/setup-editable-column-grid-view-manipulate-records/
        if (Yii::$app->request->post('hasEditable')) {
          \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
          if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('warning', "Guests cannot submit scores.");
            return ['output'=>$score->value, 'message'=>'Guests cannot submit scores.'];
          }

          $scoreId = Yii::$app->request->post('editableKey');
          $score = Score::findOne($scoreId);

          // fetch the first entry in posted data (there should only be one entry 
          // anyway in this array for an editable submission)
          // - $posted is the posted data for Book without any indexes
          // - $post is the converted array for single model validation
          $posted = current($_POST['Score']);
          $post = ['Score' => $posted];

          if ($score->load($post)) {
            $score->recorder_id = Yii::$app->user->id;
            $score->verifier_id = NULL;
            $score->verified = 0;
            $score->save();
          }

          //$response = \yii\helpers\Json::encode(['output'=>$score->value, 'message'=>'']); // required by Editable
          return ['output'=>$score->value, 'message'=>''];
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionFinish($id) {
        $model = $this->findModel($id);
        if ($model->allEntered && $model->status != 4) $model->finishGame();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Creates a new Game model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Game();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Game model.
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
     * Deletes an existing Game model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleterecurse($id)
    {
      $game = $this->findModel($id); 
      $match = $game->match;
      $game::getDb()->transaction(function($db) use ($game) {
        $game->deleteChildren();
        $game->delete();
      });

      return $this->redirect(['/match/view', 'id' => $match->id]);
    }

    /* 
     * Do whatever is the appropriate next automated step.
     * (which is currently -- choose a master selector if there isn't one.  Shrug.)
     */
    public function actionGo($id) {
      $game = $this->findModel($id);
      if ($game->master_selector == null && $game->status == 0) {
        $game->appointMasterSelect();
      } else if ($game->status == 6) {
        $game->appointMasterSelect();
      }
      return $this->redirect(['view', 'id' => $game->id]);
    }

    // master selector chooses machine.
    public function actionMastermachine($id) {
      $game = $this->findModel($id);
      if ($game->status != 0) {
        throw new \yii\base\UserException("Master selection already made!");
      }
      $game = $this->findModel($id);
      $game->machine_selector = $game->master_selector;
      $matchUsers =  $game->matchUsers;
      if ($matchUsers[0]->user_id == $game->machine_selector) {
        $game->player_order_selector = $matchUsers[1]->user_id;
      } else {
        $game->player_order_selector = $matchUsers[0]->user_id;
      }
      $game->status = 1;
      $game->save();
      return $this->redirect(['view', 'id' => $game->id]);
    }

    // master selector chooses machine.
    public function actionMasterplayer($id) {
      $game = $this->findModel($id);
      if ($game->status != 0) {
        throw new \yii\base\UserException("Master selection already made!");
      }
      $game = $this->findModel($id);
      $game->player_order_selector = $game->master_selector;
      $matchUsers =  $game->matchUsers;
      if ($matchUsers[0]->user_id == $game->player_order_selector) {
        $game->machine_selector = $matchUsers[1]->user_id;
      } else {
        $game->machine_selector = $matchUsers[0]->user_id;
      }
      $game->status = 1;
      $game->save();
      return $this->redirect(['view', 'id' => $game->id]);
    }

    public function actionCancelmachine($id) {
      $game = $this->findModel($id);
      $game->cancelSelection();
      return $this->redirect(['view', 'id' => $game->id]);
    }

    // choosing player order
    // This basically means we make the "score" objects
    public function actionPlayerorder($id, $order) {
      $game = $this->findModel($id);
      if ($game->status != 1) {
        throw new \yii\base\UserException("Wrong status for choosing player order! GameID = ". $game->id);
      }
      if ($game->playerCount != 0) {
        throw new \yii\base\UserException("Player order has already been chosen! GameID = ". $game->id);
      }
      $game::getDb()->transaction(function($db) use ($game, $order) {
        $s1 = new Score();
        $s1->playernumber = $order;
        $s1->forfeit = 0;
        $s1->verified = 0;
        $s1->game_id = $game->id;
        $s1->user_id = $game->player_order_selector;
        if (!$s1->save()) {
          Yii::error($s1->errors);
          throw new \yii\base\UserException("Error saving");
        }
        $s2 = new Score();
        $s2->playernumber = (3-$order);
        $s2->forfeit = 0;
        $s2->verified = 0;
        $s2->game_id = $game->id;
        $s2->user_id = $game->machine_selector;
        if (!$s2->save()) {
          Yii::error($s2->errors);
          throw new \yii\base\UserException("Error saving");
        }
        if ($game->machine_id != NULL) {
          $game->status = 2;
          if (!$game->save()) {
            Yii::error($game->errors);
            throw new \yii\base\UserException("Error saving");
          }
          $game->startOrEnqueueGame();
        }
      });
      return $this->redirect(['view', 'id' => $game->id]);
    }

    // selecting a machine
    public function actionSelectmachine($id, $machine_id) {
      $game = $this->findModel($id);
      if ($game->status == 0) {
        throw new \yii\base\UserException("Master selection needs to be made first! GameID = ". $game->id);
      }
      if ($game->status > 1) {
        throw new \yii\base\UserException("Machine has already been selected! GameID = ". $game->id);
      }
      if ($machine_id == -1) {
        Yii::$app->session->setFlash('error', "Invalid machine selection!");
        return $this->redirect(['view', 'id' => $game->id]);
      }
      $machine = Machine::findOne($machine_id);
      $machinestatus = $machine->machinerecentstatus;
      if ($machinestatus->status == 3) {
        throw new \yii\base\UserException("Cannot choose broken machine!");
      }
      if ($machinestatus->status == 4) {
        throw new \yii\base\UserException("Cannot choose missing machine!");
      }
      $game->machine_id = $machine_id;
      if (count($game->scores) == 0) {
        // player order has not been selected, just save the machine
        if (!$game->save()) {
          Yii::error($game->errors);
          throw new \yii\base\UserException("Error saving");
        }
      } else {
        // player order is selected already
        $game->status = 2; // awaiting machine
        if (!$game->save()) {
          Yii::error($game->errors);
          throw new \yii\base\UserException("Error saving");
        }
        $game->startOrEnqueueGame();
      }
      return $this->redirect(['view', 'id' => $game->id]);
    }

    /**
     * Finds the Game model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Game the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Game::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
