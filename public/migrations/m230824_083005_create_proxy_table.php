<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%proxy}}`.
 */
class m230824_083005_create_proxy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%proxy}}', [
            'id' => $this->primaryKey(),
            'ip' => $this->string()->notNull(),
            'port' => $this->integer()->notNull(),
            'type' => $this->string(),
            'country' => $this->string(),
            'city' => $this->string(),
            'status' => $this->integer()->defaultValue(0),
            'timeout' => $this->float(),
            'real_ip' => $this->string(),
            'check_status' => $this->integer()->defaultValue(0)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%proxy}}');
    }
}
