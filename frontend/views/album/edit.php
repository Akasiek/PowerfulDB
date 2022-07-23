<?php
/**
 * @var $model Album
 */

use common\models\Album;
use kartik\form\ActiveForm;

?>

<div class="py-10 lg:py-14 px-6 md:px-10 lg:px-20 w-full flex justify-center items-center">
    <?php $form = ActiveForm::begin() ?>

    <h1 class="form-title mb-4 mb:mb-6">Edit album <?= $model->title ?></h1>

    <div class="flex flex-col gap-6 md:gap-10 max-w-lg xl:max-w-2xl text-sm sm:text-base md:text-lg">
        <!-- RELEASE DATE INPUT -->
        <?= $form->field($model, 'release_date', [
            'errorOptions' => ['class' => 'text-red-500'],
        ])->input('date', ['class' => 'input-style']) ?>

        <!-- SUBMIT BUTTON -->
        <div class="text-right">
            <input type="submit" value="Submit" class="btn-style">
        </div>
    </div>

    <?php ActiveForm::end() ?>
</div>