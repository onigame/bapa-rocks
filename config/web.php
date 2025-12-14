<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

use \kartik\datecontrol\Module;

// customr formatter.  Not sure where to put this so it's in the config file (probably wrong)
class BapaFormatter extends \yii\i18n\Formatter
{
    public function asVaccStatus($value)
    {
        // translate your int value to something else...
        switch ($value) {
            case 0:
                return 'No card on file';
            case 1:
                return 'Card seen, not verified';
            case 2:
                return 'Card verified';
            default:
                return 'Unknown status';
        }
    }
}

$config = [
    'id' => 'basic',
    'name' => 'BAPA Manager',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
       'log',
    ],
    'timeZone' => 'America/Los_Angeles',
    'modules' => [
      'user' => [
        'class' => 'dektrium\user\Module',
        'admins' => ['onigame'],
        'modelMap' => [
          'RegistrationForm' => 'app\models\RegistrationForm',
          'User' => 'app\models\Player',
          'Profile' => 'app\models\Profile',
        ],
        'enableUnconfirmedLogin' => true,
        'confirmWithin' => 43200,
        'cost' => 12,
      ],
      'rbac' => [
        'class' => 'dektrium\rbac\RbacWebModule',
      ],
      'gridview' => [
        'class' => '\kartik\grid\Module',
      ],
      'datecontrol' => [
        'class' => 'kartik\datecontrol\Module',
        // set your display timezone
        'displayTimezone' => 'America/Los_Angeles',

        // set your timezone for date saved to db
        'saveTimezone' => 'UTC',

        // format settings for displaying each date attribute (ICU format example)
        'displaySettings' => [
            Module::FORMAT_DATE => 'yyyy-MM-dd',
            Module::FORMAT_TIME => 'hh:mm:ss a',
            Module::FORMAT_DATETIME => 'yyyy-MM-dd hh:mm:ss a', 
        ],
        
        // format settings for saving each date attribute (PHP format example)
        'saveSettings' => [
            Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
            Module::FORMAT_TIME => 'php:H:i:s',
            Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
        ],
 
        // automatically use kartik\widgets for each of the above formats
        'autoWidget' => true,
 
      ],
    ],
/*
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => [
                'Da\User\Migration',
            ],
        ],
    ],
*/
    'components' => [
        'formatter' => [
            'class' => 'BapaFormatter',
            'decimalSeparator' => '.',
            'thousandSeparator' => ',',
        ],
        'authManager' => [
            'class' => 'dektrium\rbac\components\DbManager',
//            'class' => 'yii\rbac\DbManager',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'T4p1L5tDqeyfcU5lIe6UaiduazKhoowb',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
#        'user' => [
#            'identityClass' => 'app\models\User',
#            'enableAutoLogin' => true,
#        ],
        'mailer' => [
/*
          'class' => 'boundstate\mailgun\Mailer',
          'key' => 'key-7469585775f413d87165b91ad4799871',
          'domain' => 'mg.bapa.rocks',
*/
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
//            'useFileTransport' => true,
          'viewPath' => '@app/mailer',
          'useFileTransport' => false,
          'transport' => [
            'class' => 'Swift_SmtpTransport',
/*
            'host' => 'smtp.mailtrap.io',
            'username' => 'fe4d2e810c6209',
            'password' => 'd0f9c61842989e',
            'port' => '2525',
            'encryption' => 'tls',
*/
//            'host' => gethostbyname('smtp.gmail.com'),
            'host' => 'smtp.gmail.com',
            'username' => 'admin@bapa.rocks',
            'password' => '13qe13qer',
            'port' => '587',
            'encryption' => 'tls',
//            'port' => '465',
//            'encryption' => 'ssl',
//            'streamOptions' => [
//              'ssl' => [
//                'allow_self_signed' => true,
//                'verify_peer' => false,
//                'verify_peer_name' => false,
//              ],
//            ],
          ],
//Added 2022 to help track email?
          'enableSwiftMailerLogging' => true,
            
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
//                    'categories' => ['yii\swiftmailer\Logger::add'],
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'mutex' => [
           'class' => 'yii\mutex\MysqlMutex',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
#                    'class' => 'yii\authclient\clients\GoogleHybrid',
                    'class' => 'dektrium\user\clients\Google',
                    'authUrl' => 'https://accounts.google.com/o/oauth2/auth',
                    'clientId' => '637498440800-6p0dqliq4svevi2lh7jabr3lhon8t0gc.apps.googleusercontent.com',
                    'clientSecret' => 'NW87Df2gsa3dE9PNsPyn3Fqf',
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'authUrl' => 'https://www.facebook.com/dialog/oauth?display=popup',
                    'clientId' => '769872869750264',
                    'clientSecret' => '77423ed376a271ccf145d90be24021f1',
                ],
                // etc.
            ],
        ],
        'view' => [
              'theme' => [
                  'pathMap' => [
                      '@dektrium/user/views' => '@app/views/customuser'
                  ],
             ],
         ],
        'assetManager' => [
            'bundles' => [
                'yii2mod\alert\AlertAsset' => [
                    'css' => [
                        'dist/sweetalert.css',
                        'themes/twitter/twitter.css',
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        //'allowedIPs' => ['73.93.200.170'],  // Wei-Hwa home
        'allowedIPs' => ['98.248.180.234'],  // Wei-Hwa home
        //'allowedIPs' => ['*'],  // everyone
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['98.248.180.234'],  // Wei-Hwa home
    ];
}

return $config;
