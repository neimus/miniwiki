<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id'             => 'wiki',
    'name'           => 'МиниВики',
    'language'       => 'ru',
    'sourceLanguage' => 'ru-RU',
    'basePath'       => dirname(__DIR__),
    'bootstrap'      => ['log'],
    'aliases'        => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components'     => [
        'formatter'    => [
            'class'    => 'app\components\PageFormatter',
            'timeZone' => 'Europe/Moscow',
        ],
        'request'      => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'j-K2xetAvwCNqoNh2vzrXWu_IkEpLZqb',
        ],
        'cache'        => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'page/error',
        ],
        // 'mailer'       => [
        //     'class'            => 'yii\swiftmailer\Mailer',
        //     // send all mails to a file by default. You have to set
        //     // 'useFileTransport' to false and configure a transport
        //     // for the mailer to send real emails.
        //     'useFileTransport' => true,
        // ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'           => $db,
        'urlManager' => [
            'class'           => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
                ''                 => 'page/index',
                '<action:(error|add)>' => 'page/<action>',
                ['class' => 'app\components\PageUrlRule'],
            ],
        ],
    ],
    'params'         => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
