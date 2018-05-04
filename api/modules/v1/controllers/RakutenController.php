<?php

namespace app\api\modules\v1\controllers;

use yii\base\Module;
use Affiliate\Affiliate;
use app\lib\api\Controller;
use app\lib\helpers\FileHelper;
use app\lib\helpers\StringHelper;
use app\api\modules\v1\models\Offer;
use app\api\modules\v1\models\Banner;
use app\api\modules\v1\models\Retailer;
use app\api\modules\v1\models\RetailerOffer;
use app\api\modules\v1\models\RetailerBanners;
use app\api\modules\v1\models\AffiliateIntegration;

class RakutenController extends Controller
{
    const REPORT_TYPE_SUMMARY = 'payment_history_summary';
    const REPORT_TYPE_HISTORY = 'advertiser_payment_history';
    const REPORT_TYPE_REPORTS = 'payment_details_report';

    const RETAILER_TRACKING_URL_ID = 'Mo5E0tDgkAo';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const BANNER_CATEGORY_DEFAULT = -1;
    const BANNER_START_DATE_DEFAULT = '01012016';
    const BANNER_END_DATE_DEFAULT = null;
    const BANNER_SIZE_DEFAULT = -1;
    const BANNER_CAMPAIGN_ID_DEFAULT = -1;
    const BANNER_PAGE_DEFAULT = 1;

    const MERCHANT_CROCS = 38922;

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
     * Get Access Token From A text file
     * @return mixed|null
     */
    public function getBannerRetailers()
    {
        $retailers = null;
        if (file_exists($_SERVER['DOCUMENT_ROOT'].'/banners-retailers')) {
            $retailers = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/banners-retailers');
        }

        return $retailers;
    }

    /**
     * empty file for cron checking when running endpoint..
     * @return file.txt
     */
    public function emptyFileCheck()
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/check-banner', '');
    }

    /**
     * empty file for cron checking when running endpoint..
     * @return file.txt
     */
    public function emptyRetailerCheck()
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/retailers', '[]');
    }

    /**
     * empty file for cron checking when running endpoint..
     * @return file.txt
     */
    public function emptyBannerRetailerCheck()
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/banners-retailers', '[]');
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
     * Save Access Token to a text file
     *
     * @param $retailers
     */
    public function saveBannerRetailers($retailers)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/banners-retailers', json_encode($retailers));
    }

    /**
     * @param $data
     * @param $name
     * @return string
     */
    public function saveImageFile($data, $name)
    {
        $image_url = null;

        if (@$file = file_get_contents($data))
        {
            file_put_contents(app()->params['uploadUrl'].'/'.$name, $file);
            chmod(app()->params['uploadUrl'].'/'.$name, 0775);

            $image_url = app()->params['uploadUrl'].'/'.$name;

        }

        return $image_url;
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

        $checkExist = ($this->getRetailers()) ? json_decode($this->getRetailers(), true) : null;

        foreach ($data as $key => $value) {

            $retailers = json_decode(json_encode($value));

            if (!in_array($retailers->mid, ($checkExist) ? $checkExist : [])) {

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

//                    $model->status = self::STATUS_ACTIVE;
                    if ($model->save()) {
//
                        $retOffer = RetailerOffer::findExist($model->id, $offer->id);

                        if ($retOffer) {
                            $retOffer->retailer_id = $model->id;
                            $retOffer->offer_id = $offer->id;

                            $retOffer->save();
                        }

                    }
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
            } else {

                pr($retailers->mid,0);
                /**
                 * continue looping to merchant data
                 */
                continue;
            }

            array_push($retailer, $retailers->mid);
        }

        $this->emptyFileCheck();
        if (!empty($retailer)) $this->saveRetailers($retailer);
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
        $s3     = app()->get('s3');
        $month  = date('m', time());
        $day    = date('d', time());
        $year   = date('Y', time());
//
//        $link = $s3->upload('Staging/banners/test-name/38012_201.png', $local);
//        pr($link['ObjectURL'],0);
//
//        pr(unlink($local));

        //@todo loop banners

        //@todo save banner_retailer [match affiliate_merchant_id to retailer]
        $retailers = ($this->getBannerRetailers()) ? json_decode($this->getBannerRetailers(), true) : [];
        $merchant  = Retailer::findAll(['type' => Retailer::RETAILER_TYPE_AFFILIATE]);

        $checkExist = ($this->getBannerRetailers()) ? json_decode($this->getBannerRetailers(), true) : null;

        if ($merchant) {
            foreach ($merchant as $k => $v) {

                if (!in_array($v->affiliate_merchant_id, ($checkExist) ? $checkExist : [])) {

                    /**
                     * Retailer with offer id and affiliate type
                     *
                     * @return object
                     */
                    $data = $this->model->bannerLinks(
                        $v->affiliate_merchant_id,
                        self::BANNER_CATEGORY_DEFAULT,
                        self::BANNER_START_DATE_DEFAULT,
                        self::BANNER_END_DATE_DEFAULT,
                        self::BANNER_SIZE_DEFAULT,
                        self::BANNER_CAMPAIGN_ID_DEFAULT,
                        self::BANNER_PAGE_DEFAULT
                    );

                    if ($data) {
                        foreach ($data as $key => $value) {

                            $banners = json_decode(json_encode($value));
                            $model = Banner::findBannerOrCreate($banners->linkid, $banners->mid);

                            if ($model) {

                                if ($banners->mid != self::MERCHANT_CROCS)
                                {
                                    $fileExt = ($banners->iconurl) ? FileHelper::getFileType($banners->iconurl) : null;
                                    $icon    = ($banners->iconurl) ? $this->saveImageFile($banners->iconurl, $banners->mid . '_' . $banners->linkid . '.' . $fileExt['ext']) : null;

                                    /**
                                     * Upload Banner Image to S3
                                     * Bucket Sportspass
                                     * @return Object
                                     */
                                    $s3Link = ($icon) ? $s3->upload('Staging/banners/'.Retailer::getRetailerSlugName($banners->mid).'/'.$banners->mid . '_' . $banners->linkid . '.' . $fileExt['ext'], $icon) : null;

                                    /**
                                     * Remove Banner in local file
                                     * @remove image
                                     */
                                    if ($icon)
                                    unlink($icon);

                                    $start = date('Y-m-d', strtotime($banners->startdate));
                                    $end   = date('Y-m-d', strtotime($banners->enddate));

                                    $model->type = $banners->linkname;
                                    $model->image = ($s3Link) ? $s3Link['ObjectURL'] : null;
                                    $model->affiliate_merchant_id = $banners->mid;
                                    $model->link_id = $banners->linkid;
                                    $model->url = filter_var($banners->landurl, FILTER_VALIDATE_URL) ? $banners->landurl : null;
                                    $model->tracking_url = $banners->clickurl;
                                    $model->start_date = $start;
                                    $model->end_date = $end;

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

                                    if ($model->save()) {

                                        $retBanners = RetailerBanners::findExist($banners->mid, $model->id);

                                        if ($retBanners) {

                                            $retBanners->retailer_id = Retailer::getRetailerIdByAffiliateMerchantId($banners->mid);
                                            $retBanners->banner_id = $model->id;

                                            $retBanners->save();
                                        }
                                    }

                                    sleep(1);
                                }
                            }
                        }

                        array_push($retailers, $v->affiliate_merchant_id);
                        $this->emptyFileCheck();
                        if (!empty($retailers)) $this->saveBannerRetailers($retailers);
                        break;
                    }

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

    /**
     * remove checking in textfile
     */
    public function actionRemoveChecking()
    {
        $this->emptyRetailerCheck();
        $this->emptyBannerRetailerCheck();
    }
}