<?php

namespace app\api\modules\v1\models;

use app\lib\db\ActiveRecord;

class Settings extends ActiveRecord
{
    const AWS_ACCESS_KEY  = 'access_key';
    const AWS_SECRETE_KEY = 'secrete_key';
    const AWS_BUCKET      = 'bucket';
    const AWS_REGION      = 'region';

    const FONTEND_URL    = 'frontend_url';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'],'string'],
            [[
                'key',
                'value',
                'created_at',
                'updated_at'
            ], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => t('ID'),
            'key' => t('Key'),
            'value' => t('Value'),
            'created_at' => t('Created At'),
            'updated_at' => t('Updated At')
        ];
    }

    /**
     * Get an option
     * @param array|string $key
     * @param bool $multiple set to true to return multiple options of same name
     * @return self|self[]
     */
    public static function getSettingsValue($key, $multiple = false)
    {
        $q = self::find()
            ->andWhere(['key' => $key]);

        if ($multiple)
        {
            /** @var self[] $items */
            $items = $q->all();
            return ($items) ? array_column($items, 'value') : [];
        }

        /** @var self $item */
        $item = $q->one();
        return ($item) ? $item->value : null;
    }
}