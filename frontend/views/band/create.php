<?php

/**
 * @var $model Band
 */

use common\models\Band;
use kartik\form\ActiveForm;
use yii\web\View;

$this->registerJsFile('@web/js/showBgImage.js', ['position' => View::POS_HEAD]);

$this->title = "Create Band";
?>

<div class="py-10 lg:py-14 px-6 md:px-10 lg:px-20 w-full flex justify-center items-center">
    <?php $form = ActiveForm::begin() ?>

    <h1 class="form-title mb-4 mb:mb-6">Add a band</h1>

    <div class="flex flex-col gap-6 md:gap-10 max-w-lg xl:max-w-2xl text-sm sm:text-base md:text-lg">
        <?= $this->render('_form', [
            'model' => $model,
            'form' => $form,
        ]) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>

<script>
    showBgImage('band-bg_image_url', 'user_image', '<?= Yii::getAlias('@web/resources/images/no_image.jpg') ?>');
</script>