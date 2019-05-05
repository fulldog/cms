<?php
return [
    'supportEmail' => 'admin@1.com',
    'user.passwordResetTokenExpire' => 3600,
    'site' => [
        'url' => '#',//此配置用来正确的在前台显示后台上传的文件，会被后台 设置->网站设置 网站域名覆盖
        'sign' => '###~SITEURL~###',//数据库中保存的本站地址，展示时替换成正确url
    ],
    'article.template.directory' => Yii::getAlias("@frontend/views/article"),
    'hospital_api' => [
//        'qLTL8Acri5' => [//宜昌长航医院
//            'version' => '1.0',
//            'task_api' => '//1k483932e8.51mypc.cn/chyy/admin.php',
//            'detail_api' => '//1k483932e8.51mypc.cn/chyy/admin.php',
//        ],
//        'ZqxgMgrd8u' => [//阳光医院
//            'version' => '',
//            'task_api' => '//lixingss.gicp.net/ygyy/admin.php',
//            'detail_api' => '//lixingss.gicp.net/ygyy/admin.php',
//        ],
        'hx' => [
            'version' => '',
            'task_api' => '//58.19.245.66:9090/?can=xdtj',
            'detail_api' => '//58.19.245.66:9090',
        ],
    ],
];
