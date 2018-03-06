<?php

namespace app\api\modules\v1\controllers;

use yii\base\Module;
use Affiliate\Affiliate;
use app\lib\api\Controller;

class RakutenController extends Controller
{
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
     * Get All Approved Merchant / Retailer
     *
     * @desc Save in Active Record.
     */
    public function actionMerchantByAppStatus()
    {
        $data = $this->model->merchantByAppStatus('approved');

        foreach ($data as $key => $value) {

            //@todo save retailer data in DB
            //@todo save offer / commission

            //@todo save categories and match
            if (isset($value->categories)) {
                foreach (explode(' ', $value->categories) as $category) {
                    if (is_numeric($category)) {
                        //@todo save categories here
                    }
                }
            }
        }

        pr($data);
    }

    /**
     * Get Banners For Merchant
     *
     * @return array
     */
    public function actionMerchantBannerLinks()
    {
        $data = $this->model->bannerLinks();


    }

    /**
     *
     */
    public function actionTest()
    {
        pr($this->getToken());
    }
}