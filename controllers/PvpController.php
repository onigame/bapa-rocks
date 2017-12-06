<?php

namespace app\controllers;

use Yii;
use app\models\Pvp;
use app\models\Player;
use app\models\PvpSearch;
use app\models\PvplistSearch;
use yii\filters\AccessControl;

class PvpController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['view'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [],
                        'roles' => ['Manager'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new PvplistSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($p1_id, $p2_id)
    {
        $searchModel = new PvpSearch();
        $searchModel->p1_id = $p1_id;
        $searchModel->p2_id = $p2_id;

        $p1 = Player::find()->where(['id' => $p1_id])->one();
        $p2 = Player::find()->where(['id' => $p2_id])->one();
        $p1_name = $p1->name;
        $p2_name = $p2->name;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'p1_name' => $p1_name,
            'p2_name' => $p2_name,
        ]);
    }

}
