<?php

namespace app\api\modules\v1\models;

use app\lib\helpers\Url;
use yii\web\UploadedFile;
use yii\db\Expression;
use app\lib\db\Query;
use app\lib\db\ActiveRecord;

/**
 * Class Banner
 * @property $name
 * @property $type
 * @property $is_default
 * @property $is_login
 * @property $is_hot_offer
 * @property $is_home_page
 * @property $is_new_tab
 * @property $is_trending_offers
 * @property $is_trending_experiences
 * @property $affiliate_merchant_id
 * @property $link_id
 * @property $tracking_url
 * @property $configs
 * @property $start_date
 * @property $end_date
 * @property $status
 * @property $created_at
 * @property $updated_at
 * @package app\api\modules\v1\models
 */
class Banner extends ActiveRecord
{
    const REQUEST_METHOD_PUT = 'PUT';

    /**
     * Local Path to upload
     * @var string
     */
    public $localPath = '/api/uploads/';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%banner}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'name',
                'type',
                'image',
                'url',
                'sort_order',
                'default_sort_order',
                'is_default',
                'is_login',
                'is_hot_offer',
                'is_home_page',
                'is_new_tab',
                'is_trending_offers',
                'is_trending_experiences',
                'link_id',
                'affiliate_merchant_id',
                'tracking_url',
                'configs',
                'start_date',
                'end_date',
                'status',
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
            'type' => t('Image'),
            'url' => t('Url'),
            'sort_order' => t('Sort Order'),
            'is_default' => t('Show Default'),
            'is_login' => t('Login'),
            'is_hot_offer' => t('Show Hot Offer'),
            'is_home_page' => t('Show Home Page'),
            'is_new_tab' => t('New Tab'),
            'is_trending_offers' => t('Show Trending Offers'),
            'is_trending_experiences' => t('Show Trending Experiences'),
            'link_id' => t('Link ID'),
            'affiliate_merchant_id' => t('Affiliate Merchant ID'),
            'tracking_url' => t('Tracking URL'),
            'start_date' => t('Start Date'),
            'end_date' => t('End Date'),
            'configs' => t('Configuration'),
            'status' => t('Status'),
            'created_at' => t('Created At'),
            'updated_at' => t('Updated At')
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\web\HttpException
     */
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

    /**
     * @param $link_id
     * @return static
     */
    public static function findBannerOrCreate($link_id, $merchant_id)
    {
        $obj = static::findOne([
            'link_id' => $link_id,
            'affiliate_merchant_id' => $merchant_id
        ]);
        return $obj ? null : new static;
    }
}