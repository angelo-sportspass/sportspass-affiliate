<?php

$params = require(__DIR__ . '/params.php');
$rules  = require (__DIR__.'/rules.php');

$config = [
    'id' => 'api',
    'basePath'    => dirname(__DIR__).'/..',
    'bootstrap'   => ['log'],
    'components'  => [

        // URL Configuration for our API
        'urlManager'  => [

            'enablePrettyUrl'  => true,
            // 'enableStrictParsing' => true,
            'showScriptName' => false,

            'rules' => [
                $rules,
                '/' => 'site/index',
                '<action:\w+>' => 'site/<action>',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+(-\w+)*>' => '<controller>/<action>'
            ],
        ],

        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],

        's3' => [
            'class' => 'frostealth\yii2\aws\s3\Service',
            'credentials' => [ // Aws\Credentials\CredentialsInterface|array|callable
                'key' => 'AKIAIQUIKU546XV347AA',
                'secret' => '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',
            ],
            'region' => 'ap-southeast-2',
            'defaultBucket' => 'sportspass',
            'defaultAcl' => 'public-read',
        ],

        'request' => [
            // Set Parser to JsonParser to accept Json in request
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json'  => 'yii\web\JsonParser',
            ],
        ],
        'cache'  => [
            'class'  => 'yii\caching\FileCache',
        ],

        // 'mailer' => [ ],

        // Set this enable authentication in our API
        'user' => [
            'class' => 'yii\web\User',
            'identityClass'  => 'app\models\User',
            'enableAutoLogin'  => false, // Don't forget to set Auto login to false
        ],

        // Enable logging for API in a api Directory different than web directory
        'log' => [
            'traceLevel'  => YII_DEBUG ? 3 : 0,
            'targets'  => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels'  => ['error', 'warning'],
                    // maintain api logs in api directory
                ],
            ],
        ],
        'db'  => require(__DIR__ . '/../../config/db.php'),
    ],

    'modules' => [
        'v1' => [
            'basePath' => '@app/api/modules/v1', // base path for our module class
            'class' => 'app\api\modules\v1\Api', // Path to module class
        ]
    ],

    'params'  => $params,
];

return $config;