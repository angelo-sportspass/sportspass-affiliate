<?php

namespace app\api\modules\v1\models;

use app\lib\db\ActiveRecord;

class BannerCategories extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%banner_categories}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'banner_id',
                'category_id'
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
            'category_id' => t('Category ID')
        ];
    }

    /**
     * @param $banner_id
     * @param $category_id
     * @return null|static
     */
    public static function findOrCreate($banner_id, $category_id)
    {
        $obj = null;

        if ($banner_id && $category_id)
        {
            $obj = static::findOne([
                'banner_id' => $banner_id,
                'category_id' => $category_id
            ]);
        }

        return $obj ? null : new static;
    }
}