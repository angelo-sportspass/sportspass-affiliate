<?php

use yii\db\Migration;

/**
 * Handles adding from_price to table `banner`.
 */
class m180717_050452_add_from_price_column_to_banner_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('banner', 'from_price', $this->float());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('banner', 'from_price');
    }
}
