<?php

namespace app\api\modules\v1\controllers;

use app\api\modules\v1\models\Retailer;
use Carbon\Carbon;
use yii\base\Module;
use Affiliate\Affiliate;
use app\lib\api\Controller;

class RakutenController extends Controller
{
    const REPORT_TYPE_SUMMARY = 'payment_history_summary';
    const REPORT_TYPE_HISTORY = 'advertiser_payment_history';
    const REPORT_TYPE_REPORTS = 'payment_details_report';

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

    public function saveResponseMerchant($data)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].$this->merchantFile, json_encode($data));
    }

    public function saveResponseBanner($data)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].$this->bannerFile, json_encode($data));
    }

    /**
     * Get All Approved Merchant / Retailer
     *
     * @desc Save in Active Record.
     */
    public function actionMerchantByAppStatus()
    {
        $data = $this->model->merchantByAppStatus('approved');
        pr($this->saveResponseMerchant($data));
//        foreach ($data as $key => $value) {
//
//            //@todo save retailer data in DB
//
//
//            //@todo save offer / commission
//
//            //@todo save categories and match
//            if (isset($value->categories)) {
//                foreach (explode(' ', $value->categories) as $category) {
//                    if (is_numeric($category)) {
//                        //@todo save categories here
//                    }
//                }
//            }
//        }


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
        //@todo get all retailers that are affiliate
        //@todo loop banners

        /**
         * Retailer with offer id and affiliate type
         *
         * @return object
         */
        #$data = Retailer::find()->all();

        $data = $this->model->bannerLinks(
            40988,
            -1,
            null,
            null,
            -1,
            -1,
            1
        );

        $this->saveResponseBanner($data);
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