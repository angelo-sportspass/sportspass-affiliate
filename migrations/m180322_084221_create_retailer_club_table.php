<?php

use yii\db\Migration;

/**
 * Handles the creation of table `retailer_club`.
 */
class m180322_084221_create_retailer_club_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('retailer_club', [
            'id' => $this->primaryKey(),
            'retailer_id' => $this->integer(),
            'club_id' => $this->integer(),
            'parent_id' => $this->integer(),
            'is_hide' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime()->defaultValue('0000-00-00 00:00:00'),
            'updated_at' => $this->dateTime()->defaultValue('0000-00-00 00:00:00')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('retailer_club');
    }
}
