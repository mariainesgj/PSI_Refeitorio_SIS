<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'api' => [
            'class' => 'backend\modules\api\ModuleAPI',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule','controller' => 'api/user'],
                ['class' => 'yii\rest\UrlRule','controller' => 'api/cozinha'],
                ['class' => 'yii\rest\UrlRule','controller' => 'api/ementa'],
                ['class' => 'yii\rest\UrlRule','controller' => 'api/fatura'],
                ['class' => 'yii\rest\UrlRule','controller' => 'api/linhascarrinho'],
                ['class' => 'yii\rest\UrlRule','controller' => 'api/linhasfatura'],
                ['class' => 'yii\rest\UrlRule','controller' => 'api/movimento'],
                ['class' => 'yii\rest\UrlRule','controller' => 'api/profile'],
                ['class' => 'yii\rest\UrlRule','controller' => 'api/senha'],
                ['class' => 'yii\rest\UrlRule','controller' => 'api/valor'],

            ],
        ],

    ],
    'params' => $params,
];
