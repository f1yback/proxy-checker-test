<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%proxy}}`.
 */
class m230824_100743_add_columns_to_proxy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('proxy', 'pool', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('proxy', 'pool');
    }
}
