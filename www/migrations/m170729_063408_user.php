<?php

use yii\db\Migration;

class m170729_063408_user extends Migration
{
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(128)->notNull(),
            'password' => $this->string(64),
            'authKey' => $this->string(128),
            'accessToken' => $this->string(128)
        ]);
        
        return true;
    }

    public function safeDown()
    {
        $this->dropTable('user');
        return true;
    }
}
