<?php
namespace app\lib\api;

use Yii;
use yii\filters\Cors;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\AccessControl;

class Controller extends ActiveController
{

    public $enableCsrfValidation = false;

    /**
     * @desc Parent Behavior
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);
        /**
         * @header Bearer Authentication
         */
//        $behaviors['authenticator'] = [
//            'class' => HttpBearerAuth::className(),
//        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'text/html' => Response::FORMAT_JSON,
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ],
        ];

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => [
                    'GET',
                    'POST',
                    'PUT',
                    'PATCH',
                    'DELETE'
                ],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age'           => 3600
            ],
        ];

        return $behaviors;
    }

    /**
     * return object from any request
     * @param mixed $data
     * @return mixed
     */
    public function serializeData($data)
    {
        return Yii::createObject($this->serializer)->serialize($data);
    }
}