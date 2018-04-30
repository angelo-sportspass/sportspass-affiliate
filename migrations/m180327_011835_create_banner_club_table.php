<?php

use yii\db\Migration;

/**
 * Handles the creation of table `banner_club`.
 */
class m180327_011835_create_banner_club_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('banner_club', [
            'id' => $this->primaryKey(),
            'banner_id' => $this->integer(),
            'club_id' => $this->integer(),
            'is_hide' => $this->boolean(),
            'created_at' => $this->datetime()->defaultValue('0000-00-00 00:00:00'),
            'updated_at' => $this->datetime()->defaultValue('0000-00-00 00:00:00')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('banner_club');
    }
}
