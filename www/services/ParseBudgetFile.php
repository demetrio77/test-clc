<?php 

namespace app\services;

class ParseBudgetFile
{
    const HIGHEST_COLUMN = 'M';
    private $fullPath;
    private $sheetName;
    private $error = 'Непредвиденная ошибка';
    
    public function __construct($fullPath, $sheetName)
    {
        $this->fullPath = $fullPath;
        $this->sheetName = $sheetName;
    }
    
    public function parse()
    {
        if (!file_exists($this->fullPath)) {
            return $this->setError('Файл не найден');            
        }
        
        try {
            $Data = [];

            $inputFileType = \PHPExcel_IOFactory::identify($this->fullPath);
            $ExcelReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $PhpExcel = $ExcelReader->load($this->fullPath);
            
            if (!$PhpExcel) {
                return $this->setError('Файл не является таблицей Excel');
            }
            
            $Sheet = $inputFileType!='CSV' ? $PhpExcel->getSheetByName($this->sheetName) : $PhpExcel->getActiveSheet();
            if (!$Sheet){
                return $this->setError('Отсутствует лист '.$this->sheetName);
            }
            
            $highestCell = $Sheet->getHighestRowAndColumn();
            $highestRow = $highestCell['row'];
            
            $reachedExpensesEnd = false;
            $currentCategory = null;
            $currentTotal = 0;
            $currentTarget = 0;
            
            for ($row = 1; $row <= $highestRow; $row++) {
                $ExcelRow = $Sheet->rangeToArray('A' . $row . ':' . self::HIGHEST_COLUMN. $row, false, true, false)[0];
                $Row = new ParseBudgetRow($ExcelRow);
                if ($Row->isHeader()){
                    $Data['MonthYear'] = $Row->getMonthYear();
                }
                
                if (!$reachedExpensesEnd) {
                    $reachedExpensesEnd = $Row->getExpensesEnd();
                    
                    if (!$reachedExpensesEnd && !$Row->isSkipped()){
                        $category = $Row->getCategory();
                        $categoryTotal = $Row->getCategoryTotal();
                        
                        if ($category){
                            $currentCategory = $category;
                            $currentTotal= 0;
                            $currentTarget = 0;
                        }
                        
                        if ($categoryTotal){
                            
                            if (!$currentCategory){
                                $categoryTotal['category'] = $categoryTotal['name'];
                                $Data['expenses'][] = $categoryTotal;
                            }
                            else {
                                if ($categoryTotal['targetBudget']!=$currentTarget){
                                    throw new \Exception('Для категории '.$currentCategory.' не совпала сумма в поле Target Budget');
                                }
                                if ($categoryTotal['total']!=$currentTotal){
                                    throw new \Exception('Для категории '.$currentCategory.' не совпала сумма в поле Net Totals');
                                }
                                $currentCategory = null;
                                $currentTotal = 0;
                                $currentTarget = 0;
                            }
                        }
                        else {
                            $Expense = $Row->getExpense();
                            if ($Expense) {
                                if (is_array($Expense['flightDateStart'])){
                                    if ($Expense['flightDateStart']['error_count']>0) {
                                        throw new \Exception('Ошибка в дате в ряду '.$row. ' в поле flightDateStart');
                                    }
                                    else {
                                        $date = $Expense['flightDateStart'];
                                        $Expense['flightDateStart'] = implode('-', [
                                            $date['year'],
                                            (int)$date['month']<10?'0'.$date['month']:$date['month'],
                                            (int)$date['day']<10?'0'.$date['day']:$date['day'],
                                        ]);
                                    }
                                }
                                if (is_array($Expense['flightDateEnd'])){
                                    if ($Expense['flightDateEnd']['error_count']>0){
                                        throw new \Exception('Ошибка в дате в ряду '.$row. ' в поле flightDateEnd');
                                    }
                                    else {
                                        $date = $Expense['flightDateEnd'];
                                        $Expense['flightDateEnd'] = implode('-', [
                                            $date['year'],
                                            (int)$date['month']<10?'0'.$date['month']:$date['month'],
                                            (int)$date['day']<10?'0'.$date['day']:$date['day'],
                                        ]);
                                    }
                                }
                                
                                $Expense['category'] = $currentCategory;
                                $currentTotal += $Expense['totalExpense'];
                                $currentTarget += $Expense['targetBudget'];
                                $Data['expenses'][] = $Expense;
                            }
                        }
                    }
                }
            }
            
            if (!isset($Data['MonthYear'])){
                throw new \Exception('Нет поля с годом и месяцем');
            }
            
            return $Data;
        }        
        catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
        
        return false;
    }
    
    public function setError($error)
    {
        $this->error = $error;
        return false;
    }
    
    public function getError()
    {
        return $this->error;
    }
}