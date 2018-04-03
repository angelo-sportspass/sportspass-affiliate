<?php

namespace app\api\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\api\modules\v1\models\Retailer;
use app\lib\api\Controller;

/**
 * Class RetailerController
 * @method View, Create, Update, Delete are already defined
 * @package app\api\modules\v1\controllers
 */
class RetailerController extends Controller
{
    public $modelClass = 'app\api\modules\v1\models\Retailer';

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * @param $action
     * @param $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result); // TODO: Change the autogenerated stub
        return $this->serializeData($result);
    }
}