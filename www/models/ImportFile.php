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
            'time' => 'Time',
            'file' => 'File',
            'userId' => 'User ID',
            'year' => 'Year',
            'month' => 'Month',
        ];
    }
}