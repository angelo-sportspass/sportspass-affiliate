<?php

use yii\db\Migration;

/**
 * Handles adding is_formatted to table `club`.
 */
class m180322_060846_add_is_formatted_column_to_club_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('club', 'is_formatted', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('club', 'is_formatted');
    }
}
