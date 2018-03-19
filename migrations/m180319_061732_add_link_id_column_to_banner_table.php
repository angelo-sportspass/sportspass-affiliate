<?php

use yii\db\Migration;

/**
 * Handles adding link_id to table `banner`.
 */
class m180319_061732_add_link_id_column_to_banner_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('banner', 'link_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('banner', 'link_id');
    }
}
