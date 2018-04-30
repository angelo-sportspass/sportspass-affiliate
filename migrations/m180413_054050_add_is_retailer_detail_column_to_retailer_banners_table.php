<?php

use yii\db\Migration;

/**
 * Handles adding is_retailer_detail to table `retailer_banners`.
 */
class m180413_054050_add_is_retailer_detail_column_to_retailer_banners_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('retailer_banners', 'is_retailer_detail', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('retailer_banners', 'is_retailer_detail');
    }
}
