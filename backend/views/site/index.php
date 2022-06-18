<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="site-index row">
    <div class="col d-flex flex-column">
        <a href="<?php echo \yii\helpers\Url::to('/artist') ?>" class="btn btn-primary m-2">Artist Panel</a>
        <a href="<?php echo \yii\helpers\Url::to('/artist/article') ?>" class="btn btn-primary m-2">Artist Article
            Panel</a>
    </div>

    <div class="col d-flex flex-column">
        <a href="<?php echo \yii\helpers\Url::to('/band') ?>" class="btn btn-primary m-2">Band Panel</a>
        <a href="<?php echo \yii\helpers\Url::to('/band/article') ?>" class="btn btn-primary m-2">Band Article Panel</a>
    </div>

    <div class="col d-flex flex-column">
        <a href="<?php echo \yii\helpers\Url::to('/album') ?>" class="btn btn-primary m-2">Album Panel</a>
        <a href="<?php echo \yii\helpers\Url::to('/album/article') ?>" class="btn btn-primary m-2">Album Article
            Panel</a>
    </div>
</div>
