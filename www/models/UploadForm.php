<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class UploadForm extends Model
{ 
    /**
    * @var UploadedFile
    */
    public $uploadFile;
    public $folder;
        
    public function attributeLabels()
    {
        return [
            'uploadFile' => 'Загрузите файл бюджета'
        ];
    }
    
    public function rules()
    {
        return [
            [['uploadFile'], 'file', 'extensions' => ['xls','xlsx', 'csv'], 'skipOnEmpty'=>false, 'checkExtensionByMimeType' => false, 'message' => 'Разрешены файлы с расширениями xls,xslx,csv']
        ];
    }
    
    public function checkFileName($baseName, $extension)
    {
        $i = '';
        
        do {
            $fullPath = FileHelper::normalizePath( $this->folder. '/' . $baseName . $i. '.' . $extension);
            $i++;
        }
        while (file_exists($fullPath));
        
        return $fullPath;
    }
    
    public function upload()
    {
        if (!file_exists($this->folder)){
            FileHelper::createDirectory($this->folder);
        }
        
        if ($this->validate()) {
            $fullPath = $this->checkFileName($this->uploadFile->baseName, $this->uploadFile->extension);
            $this->uploadFile->saveAs($fullPath);
            return $fullPath;
        } 
        else {
            return false;
        }
    }
}
