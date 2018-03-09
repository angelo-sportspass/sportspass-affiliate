<?php

namespace app\api\modules\v1\controllers;

use app\api\modules\v1\models\RetailerCategories;
use app\lib\helpers\StringHelper;
use Carbon\Carbon;
use yii\base\Module;
use Affiliate\Affiliate;
use app\lib\api\Controller;
use app\lib\helpers\FileHelper;
use app\api\modules\v1\models\Retailer;

class RakutenController extends Controller
{
    const REPORT_TYPE_SUMMARY = 'payment_history_summary';
    const REPORT_TYPE_HISTORY = 'advertiser_payment_history';
    const REPORT_TYPE_REPORTS = 'payment_details_report';

    const RETAILER_TRACKING_URL_ID = 'Mo5E0tDgkAo';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public $merchantFile ='/merchant';
    public $bannerFile   ='/banners';

    /**
     * Set Model For API Active Record
     * @var string
     */
    public $modelClass = "";

    /**
     * Grant Type For API
     *
     * @var string
     */
    public $type  = 'rakuten';

    /**
     * @var bool
     */
    public $isAuthenticate = false;

    /**
     * @var $model
     */
    public $model;

    public $report = [
        self::REPORT_TYPE_SUMMARY => 1,
        self::REPORT_TYPE_HISTORY => 2,
        self::REPORT_TYPE_REPORTS => 3
    ];

    /**
     * RakutenController constructor.
     * @param string $id
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);

        if (!$this->isAuthenticate) {

            $options = [
                'grant_type' => 'password',
                'username' => app()->params['rakuten_username'],
                'password' => app()->params['rakuten_password'],
                'scope' => app()->params['rakuten_scope']
            ];

            $model       = new Affiliate($this->type, $options);
            $this->model = $model->getModel();

            $this->isAuthenticate = true;
        }
    }

    public function saveResponseFile($data, $name)
    {
        $file = file_get_contents($data);

        file_put_contents(app()->params['uploadUrl'].'/'.$name, $file);
        chmod(app()->params['uploadUrl'].'/'.$name, 0775);

    }

    /**
     * Get All Approved Merchant / Retailer
     *
     * @desc Save in Active Record.
     */
    public function actionMerchantByAppStatus()
    {
        $data = $this->model->merchantByAppStatus('approved');

        foreach ($data as $key => $value) {

            //@todo save retailer data in DB
            $model = Retailer::findOrCreate($value->id);

            if ($model) {

                $model->name = $value->name;
                $model->affiliate_merchant_id = $value->mid;
                $model->type = Retailer::RETAILER_TYPE_AFFILIATE;

                if ($value->offer) {

                    $offer = new Offer;
                    $offer->name        = $value->offer->offername;
                    $offer->commission  = $value->offer->commissionterms;
                    $offer->affiliate_offer_id  = $value->offer->offerid;
                    $offer->affiliate_also_name = $value->offer->alsoname;
                    $offer->save();

                    //@todo save offer / commission
                    if (isset($value->offer->commissionterms)) {

                        $com       = explode(" ", $value->offer->commissionterms);
                        $index     = count($com);

                        $commission = StringHelper::getValueSymbol($com[$index - 1]);

                        $model->commission = $commission;
                    }
                }

                $model->status = self::STATUS_ACTIVE;

                if ($model->save()) {

                    //@todo save categories and match
                    if (isset($value->categories)) {

                        foreach (explode(' ', $value->categories) as $category) {

                            if (is_numeric($category)) {
                                //@todo save categories here
                                $retailerCategory = RetailerCategories::findExist($model->id, $category);

                                if ($retailerCategory) {

                                    $retailerCategory->retailer_id = $model->id;
                                    $retailerCategory->category_id = $category;
                                    $retailerCategory->save();
                                }
                            }
                        }
                    } // ------ check categories
                } //----- after save model
            } //----- if model not empty
        } //----- loop data response
    }

    /**
     * Get Banners For Merchant
     *
     *  $merchantId = -1,
     *  $categoryId = -1,
     *  $startDate = null,
     *  $endDate = null,
     *  $size = -1,
     *  $campaignId = -1,
     *  $page = 1
     * @return array
     */
    public function actionMerchantBannerLinks()
    {

        //@todo loop banners

        //@todo save banner_retailer [match affiliate_merchant_id to retailer]

        /**
         * Retailer with offer id and affiliate type
         *
         * @return object
         */
         $data = $this->model->bannerLinks(
            -1,
            -1,
            null,
            null,
            -1,
            -1,
            1
         );

        pr($data);
        foreach ($data as $key => $value) {

            $fileExt = FileHelper::getFileType($value->iconurl);
            #$this->saveResponseFile($value->iconurl,$value->mid.'_'.$value->linkid.'.'.$fileExt['ext']);


        }
    }

    /**
     * generate reports on transaction
     * rakuten affiliate network
     *
     * @var $startDate
     * @var $endDate
     * @var $report
     *
     * save in DB production / staging
     */
    public function actionReports()
    {
        $startDate = date('Y-m-d', strtotime('-2 days'));
        $endDate   = date('Y-m-d');

        $reports =$this->model->advancedReports(
            $this->report[self::REPORT_TYPE_SUMMARY],
            $startDate,
            $endDate
        );

        pr($reports);
    }

}