<?php 

namespace app\services;

class ParseBudgetFile
{
    private $fullPath;
    private $error;
    
    public function __construct($fullPath)
    {
        $this->fullPath = $fullPath;
    }
    
    public function parse()
    {
        if (!file_exists($this->fullPath)) {
            return $this->setError('Файл не найден');            
        }
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