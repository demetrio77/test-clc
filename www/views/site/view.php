<?php

use yii\widgets\DetailView;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/* @var app\models\ImportFile $model */
/* @var app\models\Expense $Expense */
/* @var $this yii\web\View */

$this->title = $model->name;

?><h1><?=$model->name?></h1><?php 
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'name',
        'filename',
        [
            'attribute' => 'time',
            'format' => ['date', 'php:Y-m-d H:i:s'] 
        ],
        [
            'attribute'=> 'user.username',
            'label' => 'Добавил'
        ]
    ]
]);

?><h3>Данные</h3><?php 

$Expenses = ArrayHelper::index($model->expenses, null, 'category');

?><table class="table table-bordered">
<thead>
	<tr>
		<th><b><?=$model->name?></b></th>
		<th class="text-center">campaign</th>
		<th class="text-center">target budget</th>
		<th class="text-center">Flight date start</th>
		<th class="text-center">Flight date end</th>
		<th class="text-center">Strategy</th>
		<th class="text-center">Description</th>
		<th class="text-center">Notes</th>
		<th class="text-center">Creative ID</th>
		<th class="text-center">CO-OP brand</th>
		<th class="text-center">Net Totals</th>
	</tr>
</thead>
<tbody>
	<?php foreach ($Expenses as $category => $E): ?> 
	<?php 
	$targetSum = 0;
	$totalSum = 0;
	?>
	<tr>
		<td colspan=11><b><?=$category?></b></td>
	</tr>
		<?php foreach ($E as $Expense):?>
		<?php 
		$sum = 	$Expense->targetBudget;
		$targetSum += $Expense->targetBudget;
		$totalSum += $sum;
		?>
		<tr>
			<td><?=$Expense->name?></td>
			<td><?=$Expense->campaign?></td>
			<td><?=$Expense->targetBudget?></td>
			<td><?=$Expense->flightDateStart?></td>
			<td><?=$Expense->flightDateEnd?></td>
			<td><?=$Expense->strategy?></td>
			<td><?=$Expense->description?></td>
			<td><?=$Expense->notes?></td>
			<td><?=$Expense->creativeId?></td>
			<td><?=$Expense->coopBrand?></td>
			<td><?=$sum?></td>			
		</tr>
		<?php endforeach;?>
		<tr>
			<td colspan=2>Итого</td>
			<td colspan=8><?=$targetSum?></td>
			<td><?=$totalSum?></td>			
		</tr>
	<?php endforeach;?>
</tbody>

</table>



