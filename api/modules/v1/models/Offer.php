<?php

namespace app\api\modules\v1\models;

use yii\db\Expression;
use app\lib\db\ActiveRecord;

/**
 * Class Club
 * @property $name
 * @property $commission
 * @property $affiliate_id
 * @property $configs
 * @property $created_at
 * @property $updated_at
 * @package app\api\modules\v1\models
 */
class Offer extends ActiveRecord
{
    const REQUEST_METHOD_PUT = 'PUT';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%offer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'],'string' ],
            [[
                'name',
                'commission',
                'affiliate_id',
                'configs',
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
            'name' => t('Name'),
            'commission' => t('Commission'),
            'affiliate_offer_id' => t('Affiliate Offer ID'),
            'affiliate_also_name' => t('Affiliate Also Name'),
            'created_at' => t('Created At'),
            'updated_at' => t('Updated At')
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = new Expression('NOW()');
        }

        if (app()->request->method == self::REQUEST_METHOD_PUT) {
            $this->updated_at = new Expression('NOW()');
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}