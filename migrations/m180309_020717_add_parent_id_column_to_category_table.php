<?php

use yii\db\Migration;

/**
 * Handles adding parent_id to table `category`.
 */
class m180309_020717_add_parent_id_column_to_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('category', 'parent_id', $this->integer());
        $this->addColumn('category', 'affiliate_category_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('category', 'parent_id');
        $this->dropColumn('category', 'affiliate_category_id');
    }
}
