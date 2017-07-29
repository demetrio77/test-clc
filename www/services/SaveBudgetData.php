<?php

namespace app\services;

use app\models\ImportFile;
use app\models\Expense;

class SaveBudgetData
{
    private $budgetData;
    private $fullPath;
    private $importId;
    
    public function __construct($fullPath, $budgetData)
    {
        $this->budgetData = $budgetData;
        $this->fullPath = $fullPath;
    }
    
    public function save()
    {
        $importFile = new ImportFile;
        $importFile->file = $this->fullPath;
        $importFile->month = $this->budgetData['MonthYear']['month'];
        $importFile->year = $this->budgetData['MonthYear']['year'];
        $importFile->time = time();
        $importFile->userId = \Yii::$app->user->id;
        
        if (!$importFile->save()){
            throw new \Exception('Не удалось сохранить данные файла');
        }
        
        $this->importId = $importFile->id;
        
        foreach ($this->budgetData['expenses'] as $expenseData){
            if (isset($expenseData['totalExpense'])){
                unset($expenseData['totalExpense']);
            }
            
            //При желании здесь можно сделать batchInsert
            $Expense = new Expense;
            $Expense->load($expenseData, '');
            $Expense->importId = $importFile->id;
            
            if (!$Expense->save()){
                $importFile->delete();
                throw new \Exception('Не удалось сохранить данные расходов');
            }
        }
        
        return true;
    }
    
    public function getImportId()
    {
        return $this->importId;
    }
}