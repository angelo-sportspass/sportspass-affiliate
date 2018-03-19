<?php

namespace app\api\modules\v1\controllers;

use app\api\modules\v1\models\Banner;
use app\api\modules\v1\models\RetailerCategories;
use app\lib\helpers\StringHelper;
use Carbon\Carbon;
use yii\base\Module;
use Affiliate\Affiliate;
use app\lib\api\Controller;
use app\lib\helpers\FileHelper;
use app\api\modules\v1\models\Retailer;
use app\api\modules\v1\models\Offer;
use app\api\modules\v1\models\AffiliateIntegration;

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

    /**
     * Get Access Token From A text file
     * @return mixed|null
     */
    public function getRetailers()
    {
        $retailers = null;
        if (file_exists($_SERVER['DOCUMENT_ROOT'].'/retailers')) {
            $retailers = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/retailers');
        }

        return $retailers;
    }

    /**
     * Save Access Token to a text file
     *
     * @param $retailers
     */
    public function saveRetailers($retailers)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/retailers', json_encode($retailers));
    }

    /**
     * @param $data
     * @param $name
     * @return string
     */
    public function saveImageFile($data, $name)
    {
        $file = file_get_contents($data);

        file_put_contents(app()->params['uploadUrl'].'/'.$name, $file);
        chmod(app()->params['uploadUrl'].'/'.$name, 0775);

        return app()->params['uploadUrl'].'/'.$name;
    }

    /**
     * Get All Approved Merchant / Retailer
     *
     * @desc Save in Active Record.
     */
    public function actionMerchantByAppStatus()
    {
        $retailer   = [];

        $data       = $this->model->merchantByAppStatus('approved');

        $checkExist = ($this->getRetailers()) ? json_decode($this->getRetailers(), true) : [];

        foreach ($data as $key => $value) {

            $retailers = json_decode(json_encode($value));

            if (in_array($retailers->mid, $checkExist)) {

                $model = Retailer::findRetailerOrCreate($retailers->mid);

                if ($model) {

                    $model->name = $retailers->name;
                    $model->affiliate_id = AffiliateIntegration::RAKUTEN;
                    $model->affiliate_merchant_id = $retailers->mid;
                    $model->type = Retailer::RETAILER_TYPE_AFFILIATE;

                    if ($retailers->offer) {

                        $offer = new Offer;
                        $offer->name        = $retailers->offer->offername;
                        $offer->commission  = $retailers->offer->commissionterms;
                        $offer->affiliate_id  = AffiliateIntegration::RAKUTEN;
                        $offer->configs       = json_encode([
                            $retailers->offer->offerid,
                            $retailers->offer->alsoname
                        ]);

                        $offer->save();

                        if (isset($retailers->offer->commissionterms)) {

                            $com       = explode(" ", $retailers->offer->commissionterms);
                            $index     = count($com);

                            $commission = StringHelper::getValueSymbol($com[$index - 1]);

                            $model->commission = $commission;
                        }
                    }

                    $model->status = self::STATUS_ACTIVE;
                    $model->save();
                    sleep(1);
                    //@todo comment for now to manually add retailer to each
//                    if ($model->save()) {
                        //@todo use strcmp to match category strings
                        //@todo save categories and match
//                        if (isset($retailers->categories)) {
//
//                            foreach (explode(' ', $retailers->categories) as $category) {
//
//                                if (is_numeric($category)) {
//                                    //@todo save categories here
//                                    $retailerCategory = RetailerCategories::findExist($model->id, $category);
//
//                                    if ($retailerCategory) {
//
//                                        $retailerCategory->retailer_id = $model->id;
//                                        $retailerCategory->category_id = $category;
//                                        $retailerCategory->save();
//                                    }
//                                }
//                            }
//                        } // ------ check categories
//                    }
                }
            }


            array_push($retailer, $retailers->mid);
        }

        $this->saveRetailers($retailer);
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
             39612,
            -1,
            null,
            null,
            -1,
            -1,
            1
         );

        if ($data) {
            foreach ($data as $key => $value) {

                $banners = json_decode(json_encode($value));
                $model   = Banner::findBannerOrCreate($banners->linkid, $banners->mid);

                if ($model) {

                    $fileExt = ($banners->iconurl) ? FileHelper::getFileType($banners->iconurl) : null;
                    $icon    = ($banners->iconurl) ? $this->saveImageFile($banners->iconurl,$banners->mid.'_'.$banners->linkid.'.'.$fileExt['ext']) : null;

                    $model->type = $banners->linkname;
                    $model->image = $icon;
                    $model->affiliate_merchant_id = $banners->mid;
                    $model->link_id = $banners->linkid;
                    $model->url = filter_var($banners->landurl, FILTER_VALIDATE_URL) ? $banners->landurl : null;
                    $model->tracking_url = $banners->clickurl;
                    $model->start_date = $banners->startdate;
                    $model->end_date = $banners->enddate;

                    $configs = [
                        'link_id' => $banners->linkid,
                        'link_name' => $banners->linkname,
                        'network_id' => $banners->nid,
                        'click_url' => $banners->clickurl,
                        'icon_url' => $banners->iconurl,
                        'image_url' => $banners->imgurl,
                        'land_url' => $banners->landurl,
                        'height' => $banners->height,
                        'width' => $banners->width,
                        'size' => $banners->size,
                        'server_type' => $banners->servertype
                    ];

                    $model->configs = json_encode($configs);

                    $model->save();
                    sleep(1);
                }
            }
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