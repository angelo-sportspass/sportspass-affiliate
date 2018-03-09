<?php

namespace app\api\modules\v1\models;

use yii\db\Expression;
use app\lib\db\ActiveRecord;

class RetailerCategories extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%retailer_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        //@todo define retailer current and affiliate fields
        return [
            [['name'],'string'],
            [[
                'retailer_id',
                'category_id',
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
            'category_id' => t('Category Id'),
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
            ->andWhere('category_id', [':category' => $category]);

        return $obj ?: new static;
    }
}