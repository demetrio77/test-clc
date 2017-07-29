<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "expenses".
 *
 * @property int $id
 * @property int $importId
 * @property string $category
 * @property string $name
 * @property string $campaign
 * @property string $targetBudget
 * @property string $flightDateStart
 * @property string $flightDateEnd
 * @property string $strategy
 * @property string $description
 * @property string $notes
 * @property string $creativeId
 * @property string $coopBrand
 */
class Expense extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expenses';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['importId', 'name'], 'required'],
            [['importId'], 'integer'],
            [['flightDateStart', 'flightDateEnd'], 'safe'],
            [['category', 'name', 'campaign', 'strategy', 'description', 'notes', 'creativeId', 'coopBrand'], 'string', 'max' => 255],
            ['targetBudget', 'double']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'importId' => 'Import ID',
            'category' => 'Category',
            'name' => 'Name',
            'campaign' => 'Campaign',
            'targetBudget' => 'Target Budget',
            'flightDateStart' => 'Flight Date Start',
            'flightDateEnd' => 'Flight Date End',
            'strategy' => 'Strategy',
            'description' => 'Description',
            'notes' => 'Notes',
            'creativeId' => 'Creative ID',
            'coopBrand' => 'Coop Brand',
        ];
    }
}