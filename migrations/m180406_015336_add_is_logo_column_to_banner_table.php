<?php

use yii\db\Migration;

/**
 * Handles adding is_logo to table `banner`.
 */
class m180406_015336_add_is_logo_column_to_banner_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('banner', 'is_logo', $this->boolean()->defaultValue(false));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('banner', 'is_logo');
    }
}
