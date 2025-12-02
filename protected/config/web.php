<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$modules = require __DIR__ . "/modules.php";
Yii::error('Test error from index.php', 'debug');
//a
//ab
// abc
//acds
$config = [
  'id' => 'SatuKantor',
  'name' => 'SatuKantor',
  'bootstrap' => ['log', 'queue'],
  "basePath" => dirname(__DIR__) . "/",
  "vendorPath" => dirname(__DIR__, 2) . "/vendor",
  "controllerNamespace" => "app\controllers",
  'aliases' => [
    '@bower' => '@vendor/bower-asset',
    '@npm'   => '@vendor/npm-asset',
  ],
  "timeZone" => "Asia/Jakarta",
  'language' => 'en-US',
  'sourceLanguage' => 'en-US',
  "defaultRoute" => "default",
  // 'on beforeAction' => function ($event) {
  //   return true;
  // },
  'components' => [
    'i18n' => [
      'translations' => [
        'app*' => [
          'class' => 'yii\i18n\PhpMessageSource',
          'basePath' => '@app/messages',
          'sourceLanguage' => 'en-US',
          'fileMap' => [
            'app' => 'app.php'
          ],
        ],
      ],
    ],
    'fileStorage' => [
      'class' => 'yii\components\FileStorage',
      'basePath' => '@webroot/uploads',
      'baseUrl' => '@web/uploads',
    ],
    'jwt' => [
      'class' => 'sizeg\jwt\Jwt',
      'key' => $params['TokenEncryptionKey']
    ],
    'mailer' => [
      'class' => \yii\symfonymailer\Mailer::class,
      'viewPath' => '@app/mail',
      'useFileTransport' => false,
      'transport' => [
        'scheme' => 'smtp',
        'host' => 'smtp.gmail.com',
        'username' => $params['info']['gmail']['username'],
        'password' => $params['info']['gmail']['password'],
        'port' => 465,
        'encryption' => 'ssl',
      ],
    ],
    'queue' => [
      'class' => 'yii\queue\db\Queue',
      'db' => 'db',
      'tableName' => '{{%queue}}',
      'channel' => 'default',
      'mutex' => 'yii\mutex\PgsqlMutex',
    ],
    'request' => [
      'cookieValidationKey' => 'ZBG5QqOz_ftRLeQXPSSsUMHjPQ7k9TUU',
      'parsers' => [
        'application/json' => 'yii\web\JsonParser',
      ],
    ],
    //  'response' => [
    //     'format' => yii\web\Response::FORMAT_JSON,
    //     'charset' => 'UTF-8',
    // ],
    'cache' => [
      'class' => 'yii\caching\FileCache',
    ],
    'user' => [
      "identityClass" => "app\models\User",
      "enableAutoLogin" => false,
      "authTimeout" => 3600 * 12,
      // "enableSession" => false,
      "loginUrl" => null,
      "loginUrl" => ["default/login"],
      // 'as loginOnce' => [
      //   'class' => 'app\components\LoginOnce',
      // ]
    ],
    "errorHandler" => [
      "errorAction" => "default/error",
    ],
    "authManager" => [
      "class" => 'yii\rbac\DbManager',
      "defaultRoles" => ["guest"],
    ],
    'session' => [
      'class' => 'yii\web\Session',
      // 'timeout' => 60 * 60 * 24 * 1, // 1 Day
      // 'writeCallback' => function ($session) {
      //   return [
      //     'user_id' => Yii::$app->user->id,
      //     'last_write' => time(),
      //   ];
      // },
    ],
    'log' => [
      'traceLevel' => YII_DEBUG ? 3 : 0,
      'targets' => [
        [
          'class' => 'yii\log\FileTarget',
          'levels' => ['error', 'warning'],
          'categories' => ['debug', 'jwt'], // ← ini penting!
        ],
      ],
    ],
    'assetManager' => [
      'appendTimestamp' => true,
      'bundles' => [
        'yii\web\JqueryAsset' => [
          'sourcePath' => null,
          'basePath' => '@webroot',
          'baseUrl' => '@web',
          'js' => [
            'themes/js/core/libs.min.js',
          ],
          'css' => []
        ],
        'yii\bootstrap5\BootstrapAsset' => [
          'css' => [],
          'js' => [],
        ],
        'yii\bootstrap5\BootstrapPluginAsset' => [
          'css' => [],
          'js' => [],
        ],
        'kartik\select2\ThemeKrajeeAsset' => false,
        'kartik\select2\Select2Asset' => [
          'css' => [],
        ],
      ]
    ],
    'db' => $db,
    'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false,
      'rules' => [
        'GET api/deals/<id:\d+>' => 'api/get-deals-data',
        'POST api/login' => 'ticketing/roomchat/login-api',
        'GET api/roomchat/load-chat/<id_customer:\d+>' => 'ticketing/roomchat/load-chat-json',
      ],
    ],
  ],

  'params' => $params,
  "modules" => $modules,
  "as access" => [
    "class" => "mdm\admin\components\AccessControl",
    "allowActions" => [
      "core/*",
      "ticketing/*",
      "v1/*",
      "cabang/*",
      "cuti/*",
      "debug/*",
      "gii/*",
      "datecontrol/*",
      "gridview/*",
      // "*"
    ],
  ],
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
    'allowedIPs' => ['127.0.0.1', '::1', '103.13.206.235'],
  ];
  $config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    'allowedIPs' => ['127.0.0.1', '::1', '103.13.206.235'],
    'generators' => [
      'crud' => [
        'class' => 'app\GiiCustom\crud\Generator',
        'templates' => [
          'default' => '@app/GiiCustom/crud/default',
        ]
      ],
      'module' => [
        'class' => 'yii\gii\generators\module\Generator',
        'templates' => [
          'default' => '@app/GiiCustom/module/default',
        ]
      ],
      'model' => [
        'class' => 'yii\gii\generators\model\Generator',
        'templates' => [
          'default' => '@app/GiiCustom/model/default',
        ]
      ]
    ],
  ];
}

return $config;
