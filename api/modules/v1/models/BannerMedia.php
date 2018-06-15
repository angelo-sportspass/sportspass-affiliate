<?php

namespace app\api\modules\v1\models;

use yii\db\ActiveRecord;

class BannerMedia extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%banner_media}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'banner_id',
                'media_id'
            ], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'banner_id' => t('Banner ID'),
            'media_id' => t('Media ID')
        ];
    }
}