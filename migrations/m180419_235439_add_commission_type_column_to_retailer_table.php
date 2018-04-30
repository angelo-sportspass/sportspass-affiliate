<?php

use yii\db\Migration;

/**
 * Handles adding commission_type to table `retailer`.
 */
class m180419_235439_add_commission_type_column_to_retailer_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('retailer', 'commission_type', $this->string()->defaultValue('percent'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('retailer', 'commission_type');
    }
}
