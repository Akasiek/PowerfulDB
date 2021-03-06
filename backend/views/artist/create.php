<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Artist */

$this->title = 'Create Artist';
$this->params['breadcrumbs'][] = ['label' => 'Artists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="artist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
