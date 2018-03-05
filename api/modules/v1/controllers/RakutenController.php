<?php

namespace app\api\modules\v1\controllers;


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

    public function actionMerchantByAppStatus()
    {
        $data = $this->merchantByAppStatus('approved');

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
        $response = [];

        $header[] = 'Authorization: '.self::HEADER_TYPE_BEARER. ' '. $this->getToken();
        $link  = Rakuten::BASE_API_URL.'/'.self::API_NAME.'/'.self::API_VERSION.'/'.self::MERCHANT_BY_APP_STATUS.'/'.$status;
        $curl  = new Curl;

        $response = $curl->get($link,  '', $header);

        $xmlElement = new SimpleXMLElement(XMLHelper::tidy($response));
        pr($xmlElement);
    }

    /**
     *
     */
    public function actionTest()
    {
        pr($this->getToken());
    }
}