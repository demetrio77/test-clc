<?php 

namespace app\services;

class ParseBudgetRow
{
    private $row;
    
    public function __construct($row)
    {
        $this->row = $row;
    }
    
    public function isSkipped()
    {
        return !$this->row[0] && !$this->row[1] && !$this->row[2];
    }
    
    public function isHeader()
    {
        return strcasecmp(trim($this->row[12]), 'Net Totals')===0;
    }
    
    public function getMonthYear()
    {
        $parsing = date_parse($this->row[2]);
        if (isset($parsing['year'],$parsing['month'])){
            return [
                'year'  => $parsing['year'],
                'month' => $parsing['month']
            ];
        }
        throw new \Exception('Нет поля с годом и месяцем');
    }
    
    public function getCategory()
    {
        if (!empty($this->row[1])){
            return $this->row[1];
        }
        return false;
    }
    
    public function getCategoryTotal()
    {
        if (stripos($this->row[2], 'total')!==false){
            return [
                'name' => $this->row[2],
                'targetBudget' => $this->toMoney($this->row[4]),
                'total' => $this->toMoney($this->row[12])
            ];
        }
        return false;
    }
    
    private function toMoney($str)
    {
        $str = trim($str);
        $str = trim($str, '$');
        return floatval(preg_replace('/[^\d.]/', '', $str));
    }
    
    public function getExpense()
    {
        if (!empty($this->row[2]) && !$this->getCategoryTotal() && !$this->isHeader()){
            return [
                'name' => $this->row[2],
                'campaign' => $this->row[3],
                'targetBudget' => $this->toMoney($this->row[4]),
                'flightDateStart' => !empty($this->row[5]) ? date_parse($this->row[5]) : '',
                'flightDateEnd' => !empty($this->row[6]) ? date_parse($this->row[6]) : '',
                'strategy' => $this->row[7],
                'description' => $this->row[8],
                'notes' => $this->row[9],
                'creativeId' => $this->row[10],
                'coopBrand' => $this->row[11],
                'totalExpense' => $this->toMoney($this->row[12])
            ];
        }
        
        return false;
    }
    
    public function getExpensesEnd()
    {
        return !empty($this->row[2]) && strpos($this->row[2], 'Budget Target')!==false;
    }
}