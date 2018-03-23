<?php

use yii\db\Migration;

/**
 * Handles the creation of table `location`.
 */
class m180323_004749_create_branch_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('branch', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'phone' => $this->string(),
            'position' => $this->string(),
            'email' => $this->string(),
            'branch' => $this->string(),
            'longitude' => $this->string(),
            'latitude' =>$this->string(),
            'created_at' => $this->dateTime()->defaultValue('0000-00-00 00:00:00'),
            'updated_at' => $this->dateTime()->defaultValue('0000-00-00 00:00:00')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('branch');
    }
}
