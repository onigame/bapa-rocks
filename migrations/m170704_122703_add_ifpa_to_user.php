<?php

use yii\db\Migration;
use yii\db\Schema;

class m170704_122703_add_ifpa_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%profile}}', 'ifpa', Schema::TYPE_INTEGER);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%profile}}', 'ifpa');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170704_122703_add_ifpa_to_user cannot be reverted.\n";

        return false;
    }
    */
}
