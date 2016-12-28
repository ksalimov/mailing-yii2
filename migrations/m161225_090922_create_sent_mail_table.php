<?php

use yii\db\Migration;

/**
 * Handles the creation of table `sent_mail`.
 */
class m161225_090922_create_sent_mail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('sent_mail', [
            'id' => $this->primaryKey(),
            'sender' => $this->string()->notNull(),
            'receiver' => $this->string()->notNull(),
            'subject' => $this->string()->notNull(),
            'body' => $this->string()->notNull(),
            'date' => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('sent_mail');
    }
}
