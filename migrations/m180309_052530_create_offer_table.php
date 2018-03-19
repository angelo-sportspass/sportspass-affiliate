<?php

use yii\db\Migration;

/**
 * Handles the creation of table `offer`.
 */
class m180309_052530_create_offer_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('offer', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'commission' => $this->string(),
            'affiliate_id' => $this->string(),
            'configs' => $this->text(),
            'created_at' => $this->dateTime()->defaultValue('0000-00-00 00:00:00'),
            'updated_at' => $this->dateTime()->defaultValue('0000-00-00 00:00:00')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('offer');
    }
}
