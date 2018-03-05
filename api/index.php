<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

require(__DIR__ . '/config/bootstrap.php');

require(__DIR__.'/functions.php');

$config = require(__DIR__ . '/config/api.php');

(new yii\web\Application($config))->run();