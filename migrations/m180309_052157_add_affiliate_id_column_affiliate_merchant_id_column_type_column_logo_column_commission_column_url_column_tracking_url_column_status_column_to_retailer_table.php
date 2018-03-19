<?php

use yii\db\Migration;

/**
 * Handles adding affiliate_id_column_affiliate_merchant_id_column_type_column_logo_column_commission_column_url_column_tracking_url_column_status to table `retailer`.
 */
class m180309_052157_add_affiliate_id_column_affiliate_merchant_id_column_type_column_logo_column_commission_column_url_column_tracking_url_column_status_column_to_retailer_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('retailer', 'affiliate_id', $this->integer());
        $this->addColumn('retailer', 'affiliate_merchant_id', $this->integer());
        $this->addColumn('retailer', 'type', $this->string());
        $this->addColumn('retailer', 'logo', $this->string());
        $this->addColumn('retailer', 'commission', $this->string());
        $this->addColumn('retailer', 'link', $this->string());
        $this->addColumn('retailer', 'slug_name', $this->string());
        $this->addColumn('retailer', 'status', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('retailer', 'affiliate_id');
        $this->dropColumn('retailer', 'affiliate_merchant_id');
        $this->dropColumn('retailer', 'type');
        $this->dropColumn('retailer', 'logo');
        $this->dropColumn('retailer', 'commission');
        $this->dropColumn('retailer', 'url');
        $this->dropColumn('retailer', 'tracking_url');
        $this->dropColumn('retailer', 'status');
    }
}
