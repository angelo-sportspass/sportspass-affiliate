<?php

use yii\db\Migration;

/**
 * Handles the creation of table `retailer_offer`.
 */
class m180316_072828_create_retailer_offer_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('retailer_offer', [
            'retailer_id' => $this->integer(),
            'offer_id' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('retailer_offer');
    }
}
