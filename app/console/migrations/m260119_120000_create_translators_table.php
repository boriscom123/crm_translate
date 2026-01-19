<?php

use yii\db\Migration;

/**
 * Class m260119_120000_create_translators_table
 */
class m260119_120000_create_translators_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%translators}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'weekday_availability' => $this->boolean()->defaultValue(true)->notNull(),
            'weekend_availability' => $this->boolean()->defaultValue(false)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create index for email field
        $this->createIndex(
            'idx-translators-email',
            '{{%translators}}',
            'email'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%translators}}');
    }
}