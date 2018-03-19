<?php

use yii\db\Migration;

/**
 * Handles the creation of table `retailer_category`.
 */
class m180309_052639_create_retailer_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('retailer_category', [
            'retailer_id' => $this->integer(),
            'category_id' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('retailer_category');
    }
}
