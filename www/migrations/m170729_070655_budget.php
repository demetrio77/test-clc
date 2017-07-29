<?php

use yii\db\Migration;

class m170729_070655_budget extends Migration
{
    public function safeUp()
    {
        $this->createTable('imports', [
            'id' => $this->primaryKey(),
            'time' => $this->integer()->notNull(),
            'file' => $this->string(256)->notNull(),
            'userId' => $this->integer()->notNull(),
            'year' => $this->integer(),
            'month' => $this->integer(1)
        ]);
        
        $this->createTable('expenses', [
            'id' => $this->primaryKey(),
            'importId' => $this->integer()->notNull(),
            'category' => $this->string(255),
            'name' => $this->string(255)->notNull(),
            'campaign' => $this->string(255),
            'targetBudget' => $this->string(255),
            'flightDateStart' => $this->date(),
            'flightDateEnd' => $this->date(),
            'strategy' => $this->string(255),
            'description' => $this->string(255),
            'notes' => $this->string(255),
            'creativeId' => $this->string(255),
            'coopBrand' => $this->string(255)
        ]);
        
        return true;
    }

    public function safeDown()
    {
        $this->dropTable('imports');
        $this->dropTable('expenses');

        return true;
    }
}
