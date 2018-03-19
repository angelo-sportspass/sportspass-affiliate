<?php

use yii\db\Migration;

/**
 * Handles the creation of table `retailer_deals`.
 */
class m180319_013711_create_retailer_deals_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('retailer_deals', [
            'retailer_id' => $this->integer(),
            'deal_id' => $this->integer()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('retailer_deals');
    }
}
