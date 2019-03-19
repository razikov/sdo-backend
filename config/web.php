<?php

$params = require __DIR__ . '/params.php';
if (file_exists(__DIR__ . '/_db.php')) {
    $db = require __DIR__ . '/_db.php';
} else {
    $db = require __DIR__ . '/db.php';
}
if (file_exists(__DIR__ . '/_db_ilias.php')) {
    $ilias = require __DIR__ . '/_db_ilias.php';
} else {
    $ilias = require __DIR__ . '/db_ilias.php';
}

$config = [
    'id' => 'ILIAS backend',
    'name' => 'ILIAS backend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'm81pVJmkoZqQKHVtA810Rqu5MlkJuqoR',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'ilias' => $ilias,
        'storageContainer' => [
            'class' => \app\components\StorageContainer::class,
            'storages' => [
                1 => [
                    'class' => \app\components\LocalStorage::class,
                    'basePath' => '@app/web/uploads',
                    'baseUrl' => '/uploads',
                ],
            ],
        ],
    ],
    'params' => $params,
    'language' => 'ru',
    'timeZone' => 'Europe/Moskov',
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
