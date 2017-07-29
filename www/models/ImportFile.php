<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "imports".
 *
 * @property int $id
 * @property int $time
 * @property string $file
 * @property int $userId
 * @property int $year
 * @property int $month
 * @property string $name
 * @property string $filename
 */
class ImportFile extends \yii\db\ActiveRecord
{
    public $fileUpload;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'imports';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'file', 'userId'], 'required'],
            [['time', 'userId', 'year', 'month'], 'integer'],
            [['file'], 'string', 'max' => 256],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time' => 'Время импорта',
            'file' => 'Путь к файлу',
            'filename' => 'Имя файла',
            'userId' => 'Пользователь',
            'year' => 'Год',
            'month' => 'Месяц',
            'name' => 'Год, месяц'
        ];
    }
    
    public function getName()
    {
        return $this->year. ', '. date("F",mktime(0,0,0,$this->month,1));
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
    
    public function getExpenses()
    {
        return $this->hasMany(Expense::className(), ['importId' => 'id']);
    }
    
    public function getFilename()
    {
        $path_parts = pathinfo($this->file);
        return $path_parts['basename'];
    }
}