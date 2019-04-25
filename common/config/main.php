<?php
$config = [
    'name' => 'CMS',
    'version' => '2.0.6',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'db' => [//数据库配置，这里的配置可能会被conf/db.local main-local.php配置覆盖
            'class' => yii\db\Connection::className(),
            'dsn' => 'mysql:host=localhost;dbname=hospital',
            'schemaCacheDuration' => 3600,
            'schemaCache' => 'cache',
            'username' => 'hospital',
            'password' => 'mHCa7nWKLxp2bLfB',
            'charset' => 'utf8mb4',
        ],
        'cdn' => [//支持使用 七牛 腾讯云 阿里云 网易云 具体配置请参见 http://doc.feehi.com/cdn.html
            'class' => feehi\cdn\DummyTarget::className(),//不使用cdn
        ],
        'cache' => [//缓存组件
//            'class' => yii\caching\FileCache::className(),
            'class' => 'yii\redis\Cache',//使用redis缓存作为项目缓存
            'redis' => [//配置redis
                'hostname' => '127.0.0.1',
                'port' => '6379',
                'database' => 0,
                'password' => 'hospital',
            ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 6,
            'password' => 'hospital',
        ],
        'formatter' => [//格式显示配置
            'dateFormat' => 'php:Y-m-d H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CHY',
            'nullDisplay' => '-',
            'datetimeFormat' => 'php:Y-m-d H:i:s'
        ],
        'mailer' => [//邮箱发件人配置，会被main-local.php以及后台管理页面中的smtp配置覆盖
            'class' => yii\swiftmailer\Mailer::className(),
            'viewPath' => '@common/mail',
            /*特别注意：如果useFileTransport为true，并不会真发邮件，只会把邮件写入runtime目录，很有可能造成您的磁盘使用飙升。
                        如果为false，当您配置的smtp地址不存在或错误，页面会一直等到连接邮件服务器超时才会输出页面。*/
            'useFileTransport' => true,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.feehi.com',  //每种邮箱的host配置不一样
                'username' => 'admin@feehi.com',
                'password' => 'password',
                'port' => '586',
                'encryption' => 'tls',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['admin@i.com' => ' CMS robot ']
            ],
        ],
        'feehi' => [
            'class' => feehi\components\Feehi::className(),
        ],
        'authManager' => [
            'class' => yii\rbac\DbManager::className(),
        ],
        'assetManager' => [
            'linkAssets' => false,
            'bundles' => [
                yii\widgets\ActiveFormAsset::className() => [
                    'js' => [
                        'a' => 'yii.activeForm.js'
                    ],
                ],
                yii\bootstrap\BootstrapAsset::className() => [
                    'css' => [],
                    'sourcePath' => null,
                ],
                yii\captcha\CaptchaAsset::className() => [
                    'js' => [
                        'a' => 'yii.captcha.js'
                    ],
                ],
                yii\grid\GridViewAsset::className() => [
                    'js' => [
                        'a' => 'yii.gridView.js'
                    ],
                ],
                yii\web\JqueryAsset::className() => [
                    'js' => [
                        'a' => 'jquery.js'
                    ],
                ],
                yii\widgets\PjaxAsset::className() => [
                    'js' => [
                        'a' => 'jquery.pjax.js'
                    ],
                ],
                yii\web\YiiAsset::className() => [
                    'js' => [
                        'a' => 'yii.js'
                    ],
                ],
                yii\validators\ValidationAsset::className() => [
                    'js' => [
                        'a' => 'yii.validation.js'
                    ],
                ],
            ],
        ],
    ],
];
$install = Yii::getAlias('@common/config/conf/db.php');
if (file_exists($install)) {
    return yii\helpers\ArrayHelper::merge($config, (require $install));
}
return $config;