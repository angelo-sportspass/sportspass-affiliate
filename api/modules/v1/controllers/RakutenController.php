<?php

namespace app\api\modules\v1\controllers;

use Carbon\Carbon;
use yii\base\Module;
use Affiliate\Rakuten;
use SimpleXMLElement;
use Affiliate\Affiliate;
use app\lib\helpers\Curl;
use app\lib\api\Controller;
use Affiliate\Helpers\XMLHelper;

class RakutenController extends Controller
{
    const API_NAME    = 'linklocator';
    const API_VERSION = '1.0';

    const MERCHANT_BY_ID         = 'getMerchByID';
    const MERCHANT_BY_NAME       = 'getMerchByName';
    const MERCHANT_BY_CATEGORY   = 'getMerchByCategory';
    const MERCHANT_BY_APP_STATUS = 'getMerchByAppStatus';
    const CREATIVE_CATEGORIES    = 'getCreativeCategories';
    const TEXT_LINKS             = 'getTextLinks';
    const BANNER_LINKS           = 'getBannerLinks';
    const DRM_LINKS              = 'getDRMLinks';
    const PRODUCT_LINKS          = 'getProductLinks';

    const VALID_SUB_APIS = [
        self::MERCHANT_BY_ID,
        self::MERCHANT_BY_NAME,
        self::MERCHANT_BY_CATEGORY,
        self::MERCHANT_BY_APP_STATUS,
        self::CREATIVE_CATEGORIES,
        self::TEXT_LINKS,
        self::BANNER_LINKS,
        self::DRM_LINKS,
        self::PRODUCT_LINKS,
    ];

    const HEADER_TYPE_BEARER = 'Bearer';

    /**
     * Set Model For API Active Record
     * @var string
     */
    public $modelClass = "";

    /**
     * Grant Type For API
     * @var string
     */
    public $type  = 'rakuten';

    /**
     * @var bool
     */
    public $isAuthenticate = false;

    /**
     * @var $token
     */
    protected $modelData;

    /**
     * @var $accessToken
     */
    protected $accessToken;

    /**
     * @var $model
     */
    public $model;

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

            $model = new Affiliate($this->type, [
                'grant_type' => 'password',
                'username' => app()->params['rakuten_username'],
                'password' => app()->params['rakuten_password'],
                'scope' => app()->params['rakuten_scope']
            ]);

            $this->model     = $model->getModel();
            $this->modelData = json_decode($this->model->getToken());

            $this->setToken($this->modelData->access_token);
            $this->isAuthenticate = true;
        }
    }

    /**
     * Get Access Token
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->accessToken;
    }

    /**
     * Set Access Token For header
     *
     * @param $token
     */
    public function setToken($token)
    {
        $this->accessToken = $token;
    }

    /**
     * Get All Approved Merchant / Retailer
     *
     * @desc Save in Active Record.
     */
    public function actionMerchantByAppStatus()
    {
        $data = $this->merchantByAppStatus('approved');

        //@todo save retailer data in DB
        //@todo save offer / commission
        //@todo save categories and match
        //@todo get banners links

        if ($data) {
            pr($data);
        }
    }

    /**
     * Allows you to download advertiser information by specifying
     * the Application Status ID for the Application Status that
     * you want to get the List of Merchants for.
     *
     * Application status options:
     *   approved
     *   approval extended
     *   wait
     *   temp removed
     *   temp rejected
     *   perm removed
     *   perm rejected
     *   self removed
     *
     * @param string $status
     *
     * @return array
     */
    public function merchantByAppStatus($status)
    {
        $header[] = 'Authorization: '.self::HEADER_TYPE_BEARER. ' '. $this->getToken();
        $link  = Rakuten::BASE_API_URL.'/'.self::API_NAME.'/'.self::API_VERSION.'/'.self::MERCHANT_BY_APP_STATUS.'/'.$status;
        $curl  = new Curl;

        $response = $curl->get($link,  '', $header);

        $xmlElement = new SimpleXMLElement(XMLHelper::tidy($response));
        pr($xmlElement);
    }

    /**
     * Allows you to download an advertiser’s information by specifying
     * the LinkShare Advertiser ID for that advertiser.
     *
     * @param int $merchantId The LinkShare Advertiser ID
     *
     * @return $merchantId
     */
    public function merchantById($merchantId)
    {
        //@todo Implementation here

        return $merchantId;
    }

    /**
     * Allows you to download an advertiser’s information by specifying the name of the advertiser.
     *
     * @param string $name The name of the advertiser. It must be an exact match.
     *
     * @return $name
     */
    public function merchantByName($name)
    {
        //@todo Implementation here
        return $name;
    }

    /**
     * Allows you to download advertiser information by specifying the advertiser category.
     *
     * These are the same categories that you see when looking for advertisers in the
     * Programs section of the Publisher Dashboard.
     *
     * @param int $categoryId The category of the advertiser
     *
     * @return $categoryId
     */
    public function merchantByCategory($categoryId)
    {
        //@todo Implementation here
        return $categoryId;
    }

    /**
     * Provides you the available banner links.
     *
     * To obtain specific banner links, you can filter this request using
     * these parameters: MID, Category, Size, Start Date, and End Date.
     *
     * @param int         $merchantId This is the Rakuten LinkShare Advertiser ID.
     *                                Optional, use -1 as the default value.
     * @param int         $categoryId This is the Creative Category ID.
     *                                It is assigned by the advertiser. Use the Creative Category
     *                                feed to obtain it (not the Advertiser Category Table listed
     *                                in the Publisher Help Center).
     *                                Optional, use -1 as the default value.
     * @param Carbon|null $startDate  This is the start date for the creative, formatted MMDDYYYY.
     *                                Optional, use null as the default value.
     * @param Carbon|null $endDate    This is the end date for the creative, formatted MMDDYYYY.
     *                                Optional, use null as the default value.
     * @param int         $size       This is the banner size code.
     *                                Optional, use -1 as the default value.
     * @param int         $campaignId Rakuten LinkShare retired this feature in August 2011.
     *                                Please enter -1 as the default value.
     * @param int         $page       This is the page number of the results.
     *                                On queries with a large number of results, the system
     *                                returns 10,000 results per page. This parameter helps
     *                                you organize them.
     *                                Optional, use 1 as a default value.
     *
     * @return $data[]
     */
    public function bannerLinks(
        $merchantId = -1,
        $categoryId = -1,
        Carbon $startDate = null,
        Carbon $endDate = null,
        $size = -1,
        $campaignId = -1,
        $page = 1
    ) {
        $data = [];

        return $data;
    }

    /**
     *
     */
    public function actionTest()
    {
        pr($this->getToken());
    }
}