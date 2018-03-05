<?php
namespace app\lib\helpers;

use Yii;
use yii\helpers\BaseUrl;

/**
 * Class Url
 * @package app\lib\helpers
 *
 * @author Angelo <angelo@sportspass.com.au>
 */
class Url extends BaseUrl
{
    /**
     * @param string $url
     * @param bool $scheme
     * @param bool $programSuffix
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function to($url = '', $scheme = false, $programSuffix=true)
    {
        if (!$programSuffix)
        {
            // change base url to remove suffix so we can get original
            $baseUrl = app()->urlManager->getBaseUrl();
            app()->urlManager->setBaseUrl(null);
            $_url = parent::to($url, $scheme);
            app()->urlManager->setBaseUrl($baseUrl);
        }
        else
        {
            $_url = parent::to($url, $scheme);
        }

        return $_url;
    }
}