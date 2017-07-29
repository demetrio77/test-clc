<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Test CLC';

$form = ActiveForm::begin();

echo $form->field($model, 'uploadFile')->fileInput();

echo Html::submitButton('Загрузить', ['class' => 'btn btn-primary']);

ActiveForm::end();