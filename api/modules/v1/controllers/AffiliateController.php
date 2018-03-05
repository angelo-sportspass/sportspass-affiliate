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

    public function actionGenerateModel()
    {
        $model = new Affiliate('rakuten', [
            'grant_type' => 'password',
            'username' => 'SportsPass',
            'password' => 'Turbo100',
            'scope' => '3222890'
        ]);

        return $model->getModel();
    }
}