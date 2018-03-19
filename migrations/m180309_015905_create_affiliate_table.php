<?php

use yii\db\Migration;

/**
 * Handles the creation of table `affiliate`.
 */
class m180309_015905_create_affiliate_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('affiliate', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'tracking_link' => $this->string(255),
            'tracking_id' => $this->string(50),
            'configs' => $this->text(),
            'created_at' => $this->dateTime()->defaultValue('0000-00-00 00:00:00'),
            'updated_at' => $this->dateTime()->defaultValue('0000-00-00 00:00:00')
        ]);

        $this->insert('affiliate', [
            'name' => 'Rakuten Linkshare',
            'tracking_link' => 'http://click.linksynergy.com/fs-bin/click',
            'tracking_id' => 'Mo5E0tDgkAo',
            'created_at' => new \yii\db\Expression('NOW()')
        ]);

        $this->insert('affiliate', [
            'name' => 'Commission Factory',
            'tracking_link' => 'https://t.cfjump.com',
            'tracking_id' => '20446',
            'created_at' => new \yii\db\Expression('NOW()')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('affiliate');
    }
}
