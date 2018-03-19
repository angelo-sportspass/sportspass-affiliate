<?php

use yii\db\Migration;

/**
 * Handles adding link_id to table `banner`.
 */
class m180316_075457_add_link_id_column_to_banner_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('banner', 'affiliate_merchant_id', $this->integer());
        $this->addColumn('banner', 'tracking_url', $this->string(255));
        $this->addColumn('banner', 'configs', $this->text());
        $this->addColumn('banner', 'start_date', $this->date());
        $this->addColumn('banner', 'end_date', $this->date());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('banner', 'affiliate_merchant_id');
        $this->dropColumn('banner', 'tracking_url');
        $this->dropColumn('banner', 'configs');
        $this->dropColumn('banner', 'start_date');
        $this->dropColumn('banner', 'end_date');
    }
}
