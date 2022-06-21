<?php

namespace frontend\controllers;

use common\models\Artist;
use common\models\ArtistArticle;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\filters\AccessControl;
use yii\web\Controller;

class ArtistController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $sort = new Sort([
            'attributes' => [
                'name' => [
                    'asc' => ['artist.name' => SORT_ASC],
                    'desc' => ['artist.name' => SORT_DESC],
                ],
                'birth_date' => [
                    'asc' => ['artist.birth_date' => SORT_ASC],
                    'desc' => ['artist.birth_date' => SORT_DESC],
                ],
            ],
            'defaultOrder' => ['name' => SORT_ASC],
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => Artist::find()->orderBy($sort->orders),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'sort' => $sort,
        ]);
    }

    public function actionView($slug)
    {
        $model = Artist::findOne(['slug' => $slug]);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new Artist();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'slug' => $model->slug]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionArticleCreate($slug)
    {
        $model = new ArtistArticle();
        $artist = Artist::findOne(['slug' => $slug]);

        if ($model->load(\Yii::$app->request->post())) {
            $model->artist_id = $artist->id;
            if ($model->save()) {
                return $this->redirect(['view', 'slug' => $slug]);
            }
        } else {
            return $this->render('article/create', [
                'model' => $model,
            ]);
        }
    }
}
