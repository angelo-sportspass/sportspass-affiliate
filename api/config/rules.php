<?php
/**
 * API rules and routes
 * add specific routes and url parameters.
 */
return [
    'class'  => 'yii\rest\UrlRule',
    'controller'  => [
        'v1/affiliate',
        'v1/rakuten',
        'v1/hot-deals'
    ],
    'pluralize' => false,
    'extraPatterns' => [

        /**
         * @todo add function with parameters here
         * @parttern <params> / <function-name> => <controller-function>
         */


    ],
];