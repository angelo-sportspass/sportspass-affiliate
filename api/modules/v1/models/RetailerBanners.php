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
     * @param $banner
     *
     * @return object
     */
    public static function findExist($retailer, $banner)
    {
        $obj = self::findOne([
           'retailer_id' => $retailer,
           'banner_id' => $banner
        ]);

        return $obj ? null : new static;
    }
}