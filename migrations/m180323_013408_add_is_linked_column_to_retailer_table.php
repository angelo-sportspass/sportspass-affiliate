<?php

use yii\db\Migration;

/**
 * Handles adding is_linked to table `retailer`.
 */
class m180323_013408_add_is_linked_column_to_retailer_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('retailer', 'is_linked', $this->boolean()->defaultValue(false));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('retailer', 'is_linked');
    }
}
