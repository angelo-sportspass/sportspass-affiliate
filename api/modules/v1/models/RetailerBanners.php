<?php

namespace app\api\modules\v1\models;

use app\lib\db\ActiveRecord;

class RetailerBanners extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%retailer_banners}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        //@todo define retailer current and affiliate fields
        return [
            [[
                'retailer_id',
                'banner_id',
            ], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        //@todo define retailer current and affiliate fields
        return [
            'retailer_id' => t('Retailer Id'),
            'banner_id' => t('Banner Id'),
        ];
    }

    /**
     * @param $retailer
     * @param $category
     *
     * @return object
     */
    public static function findExist($retailer, $category)
    {
        $obj = self::find()
            ->where('retailer_id = :retailer', [':retailer' => $retailer])
            ->andWhere('banner_id = :banner_id', [':banner_id' => $category]);

        return $obj ?: new static;
    }
}