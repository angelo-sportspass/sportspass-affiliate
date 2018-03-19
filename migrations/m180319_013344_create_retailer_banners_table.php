<?php

use yii\db\Migration;

/**
 * Handles the creation of table `retailer_banners`.
 */
class m180319_013344_create_retailer_banners_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('retailer_banners', [
            'retail_id' => $this->integer(),
            'banner_id' => $this->integer()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('retailer_banners');
    }
}
