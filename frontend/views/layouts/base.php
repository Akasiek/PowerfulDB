<?php

/**
 * @var View $this
 * @var string $content
 */

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\web\View;


AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>

        <!-- GOOGLE FONTS -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400;1,700&family=Staatliches&display=swap"
              rel="stylesheet">

        <!-- GOOGLE ICONS -->
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0"/>
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,700,0,0"/>


        <!-- SWIPER.JS -->
        <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>

        <style>
            :root {
                --swiper-theme-color: #4EFFA6;
                --swiper-navigation-size: 30px;
            }

            .swiper-button-next::after, .swiper-button-prev::after {
                display: none;
            }
        </style>

    </head>
    <body class="bg-secondary-dark text-main-light font-serif">
    <?php $this->beginBody() ?>


    <?php echo $content ?>


    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage();