<?php

use yii\db\Migration;

/**
 * Handles the creation of table `retailer_location`.
 */
class m180323_004814_create_retailer_branch_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('retailer_branch', [
            'retailer_id' => $this->integer(),
            'branch_id' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('retailer_branch');
    }
}
