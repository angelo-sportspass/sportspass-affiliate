<?php

namespace app\api\modules\v1\models;

use app\lib\db\ActiveRecord;

class RetailerOffer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%retailer_offer}}';
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
                'offer_id',
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
            'offer_id' => t('Offer Id'),
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
            ->andWhere('offer_id = :offer_id', [':offer_id' => $category]);

        return $obj ?: new static;
    }
}