<?php

namespace frontend\controllers;

use common\models\Album;
use common\models\AlbumArticle;
use common\models\AlbumGenre;
use common\models\EditSubmission;
use common\models\FeaturedAuthor;
use common\models\Track;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\helpers\Url;
use yii\web\Controller;

include \Yii::getAlias('@frontend/web/checkModelDiff.php');
include \Yii::getAlias('@frontend/web/arrayEqual.php');

class AlbumController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [
                    'create',
                    'edit',
                    'article-create',
                    'genre-add',
                    'track-add'
                ],
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
                'popularity' => [
                    'desc' => ['album.views' => SORT_DESC],
                ],
                'title' => [
                    'asc' => ['album.title' => SORT_ASC],
                    'desc' => ['album.title' => SORT_DESC],
                ],
                'release_date' => [
                    'asc' => ['album.release_date' => SORT_ASC],
                    'desc' => ['album.release_date' => SORT_DESC],
                ],
            ],
            'defaultOrder' => ['release_date' => SORT_DESC],
        ]);

        $query = Album::find()
            ->leftJoin('artist', 'artist.id = album.artist_id')
            ->leftJoin('band', 'band.id = album.band_id')
            ->leftJoin('album_genre', 'album_genre.album_id = album.id')
            ->leftJoin('genre', 'genre.id = album_genre.genre_id')
            ->orderBy($sort->orders)
            ->distinct();


        // Check if any filters are set
        $filters = \Yii::$app->request->get();
        if (isset($filters['release_from_year']) && $filters['release_from_year'] != '') {
            $query->andWhere('EXTRACT(YEAR FROM release_date) >= :from_year', [':from_year' => $filters['release_from_year']]);
        }
        if (isset($filters['release_to_year']) && $filters['release_to_year'] != '') {
            $query->andWhere('EXTRACT(YEAR FROM release_date) <= :to_year', [':to_year' => $filters['release_to_year']]);
        }
        if (isset($filters['genre']) && $filters['genre'] != '') {
            $query->andWhere('genre.name ILIKE :genre', [':genre' => '%' . trim($filters['genre']) . '%']);
        }
        if (isset($filters['type']) && $filters['type'] != '') {
            $query->andWhere(array('IN', 'album.type', $filters['type']));
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 24,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'sort' => $sort,
        ]);
    }

    public function actionView($slug)
    {
        $model = Album::find()->where(['slug' => $slug])->with('artist', 'band')->one();

        // If user refreshed site, don't count view
        if (Url::current() !== Url::previous()) {
            $model->views += 1;
            $model->save();
            Url::remember();
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new Album();

        if ($model->load(\Yii::$app->request->post())) {

            // Check if author_id is set. If not, send an error message.
            $author_id = \Yii::$app->request->post('author_id');
            if ($author_id == '') {
                \Yii::$app->session->setFlash('author_id', 'Please select an artist or a band.');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            // Check if author is an artist or band and set the appropriate id
            $author = explode('-', $author_id);
            if ($author[0] == 'artist') $model->artist_id = $author[1];
            else $model->band_id = $author[1];

            // Set type of album
            $model->type = \Yii::$app->request->post('type');

            // Check if the album already exists
            $duplicate = Album::find()
                ->where(
                    'artist_id = :artist_id OR band_id = :band_id',
                    [':artist_id' => $model->artist_id, ':band_id' => $model->band_id]
                )
                ->andWhere('title = :title', [':title' => $model->title])
                ->one();
            if ($duplicate) {
                \Yii::$app->session->setFlash('duplicate', 'This album already exists.');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->save()) {
                return $this->redirect(['view', 'slug' => $model->slug]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionEdit($slug)
    {
        $model = Album::find()->where(['slug' => $slug])->one();
        
        if ($model->load(\Yii::$app->request->post())) {
            // Add type to model from post
            $model->type = \Yii::$app->request->post('type');

            // Check for differences in models
            $diff = checkModelDiff($model);
            foreach ($diff as $column => $value) {
                $submission = new EditSubmission();
                $submission->setValues('album', $column, $model->id, $value['old'], $value['new']);
                $submission->saveSubmission();
            }

            // Get model artist or band and add prefix to it
            if ($model->artist_id) $modelAuthor = 'artist-' . $model->artist_id;
            else $modelAuthor = 'band-' . $model->band_id;

            if (\Yii::$app->request->post('author_id') !== $modelAuthor) {
                $author = \Yii::$app->request->post('author_id');
                $submission = new EditSubmission();
                $submission->setValues('album', 'author_id', $model->id, $modelAuthor, $author);
                $submission->saveSubmission();
            }

            return $this->redirect(['view', 'slug' => $model->slug]);
        }
        return $this->render('edit', [
            'model' => $model,
        ]);

    }

    public function actionArticleCreate($slug)
    {
        $model = new AlbumArticle();

        $album = Album::findOne(['slug' => $slug]);

        if ($model->load(\Yii::$app->request->post())) {
            $model->album_id = $album->id;
            if ($model->save()) {
                return $this->redirect(['view', 'slug' => $slug]);
            }
        } else {
            return $this->render('article/create', [
                'model' => $model,
            ]);
        }
    }

    public function actionGenreAdd($slug)
    {
        $album = Album::findOne(['slug' => $slug]);

        $genres = \Yii::$app->request->post('genres');
        if ($genres) {
            foreach ($genres as $genre) {
                $albumGenre = new AlbumGenre();
                $albumGenre->genre_id = $genre;
                $albumGenre->album_id = $album->id;
                $albumGenre->save();
            }
            return $this->redirect(['/album/view', 'slug' => $slug]);
        } else {
            return $this->render('genre/add', [
                'album' => $album,
            ]);
        }
    }

    public function actionGenreEdit($slug)
    {
        $album = Album::findOne(['slug' => $slug]);
        $albumGenres = AlbumGenre::find()->where(['album_id' => $album->id])->all();

        if (\Yii::$app->request->post()) {
            // Create array of new and old genres
            $newGenres = \Yii::$app->request->post('genres');
            $oldGenres = [];
            foreach ($albumGenres as $genre) {
                $oldGenres[] = $genre->genre_id;
            }

            // Check if arrays are identical, if yes, don't create new submission
            if (arrayEqual($oldGenres, $newGenres)) {
                \Yii::$app->session->setFlash('error', 'No changes made.');
                return $this->redirect(['/album/view', 'slug' => $slug]);
            }

            // Create string representation of genre arrays
            $newData = '[' . implode(', ', $newGenres) . ']';
            $oldData = '[' . implode(', ', $oldGenres) . ']';

            // Create submission
            $submission = new EditSubmission();
            $submission->setValues('album', 'genre_id', $album->id, $oldData, $newData);
            $submission->saveSubmission();

            return $this->redirect(['/album/view', 'slug' => $slug]);
        } else {
            return $this->render('genre/edit', [
                'album' => $album,
                'albumGenres' => $albumGenres,
            ]);
        }

    }

    public function actionTrackAdd($slug)
    {
        $album = Album::findOne(['slug' => $slug]);

        $tracks = \Yii::$app->request->post('tracks');
        if ($tracks) {
            $tracksDuration = \Yii::$app->request->post('tracks_duration');
            $featuredAuthorId = \Yii::$app->request->post('featured_author_id');

            foreach ($tracks as $position => $track) {
                $trackModel = new Track();
                $trackModel->album_id = $album->id;
                $trackModel->title = $track;
                $trackModel->duration = $tracksDuration[$position];
                $trackModel->position = $position;
                $trackModel->save();
            }

            if (isset($featuredAuthor)) {
                foreach ($featuredAuthorId as $authorTrackPos => $featuredAuthor) {
                    $authorModel = new FeaturedAuthor();

                    // Find track for this author
                    $authorTrack = Track::find()
                        ->where(['album_id' => $album->id, 'position' => $authorTrackPos])->one();
                    $authorModel->track_id = $authorTrack->id;

                    // Check if author is an artist or band and set the appropriate id
                    $author = explode('-', $featuredAuthor);
                    if ($author[0] == 'artist') {
                        $authorModel->artist_id = $author[1];
                    } else {
                        $authorModel->band_id = $author[1];
                    }
                    $authorModel->save();
                }
            }
            return $this->redirect(['/album/view', 'slug' => $slug]);
        } else {
            return $this->render('track/add', [
                'album' => $album,
            ]);
        }
    }
}
