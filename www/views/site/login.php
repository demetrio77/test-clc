<?php 

use yii\authclient\widgets\AuthChoice;

?><?=AuthChoice::widget([
    'baseAuthUrl' => ['site/auth'],
    'popupMode' => false,
])?><b>Войдите, используя учётную запись Google</b>
<style>
.auth-clients {
	width:120px;
}
</style>