<?php 

echo yii\authclient\widgets\AuthChoice::widget([
    'baseAuthUrl' => ['site/auth'],
    'popupMode' => false,
]) ;

var_dump(Yii::$app->user->isGuest);