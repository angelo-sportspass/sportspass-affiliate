<?php
/**
 * Initiates Migration Command :
 * Add this for migration unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock
 */
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1:8889;dbname=test;',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
];
