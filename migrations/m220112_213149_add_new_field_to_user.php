<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m220112_213149_add_new_field_to_user
 */
class m220112_213149_add_new_field_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%user}}', 'check', Schema::TYPE_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('{{%user}}', 'check');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220112_213149_add_new_field_to_user cannot be reverted.\n";

        return false;
    }
    */
}
