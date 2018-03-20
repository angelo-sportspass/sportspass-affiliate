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
     * @param $offer
     *
     * @return object
     */
    public static function findExist($retailer, $offer)
    {
        $obj = self::findOne([
            'retailer_id' => $retailer,
            'offer_id' => $offer
        ]);

        return $obj ? null : new static;
    }
}