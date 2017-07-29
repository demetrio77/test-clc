<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */

$this->title = 'Test CLC';

?>
<div class="clc-index">
    <p>
    	<?= Html::a('Загрузить файл', ['upload'], ['class'=>"btn btn-primary"]) ?>
    </p>
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
 		'columns' => [
   			[
				'attribute' => 'id',
   			    'headerOptions' => ['style' => 'width:40px;']
   			],
            [
                'attribute' => 'name',
                'format'=>'raw',
   				'value' => function($data) {
   				     return Html::a($data->name, ['view', 'id' => $data->id]);
				}
            ],
            [
                'attribute' => 'time',
                'format'=> ['date', 'php:Y-m-d H:i:s'] 
            ],
        ],
    ]); ?>
</div>
<?php 
