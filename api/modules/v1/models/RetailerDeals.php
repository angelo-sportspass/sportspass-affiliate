<?php

namespace app\api\modules\v1\models;

use app\lib\db\ActiveRecord;

class RetailerDeals extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%retailer_deals}}';
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
                'deal_id',
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
            'deal_id' => t('Deal Id'),
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
            ->andWhere('deal_id = :deal_id', [':deal_id' => $category]);

        return $obj ?: new static;
    }
}