<?php

namespace app\api\modules\v1\controllers;

use Affiliate\Affiliate;
use app\lib\api\Controller;

class AffiliateController extends Controller
{
    /**
     * set Model Class if has Model
     *
     * @var string
     */
    public $modelClass = "";

    /**
     * Test Purposes
     * @return \Affiliate\Commission
     */
    public function actionGenerateModel()
    {
        $model = new Affiliate('rakuten', [
        ]);

        return $model->getModel();
    }
}