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
        'qLTL8Acri5' => [
            'version' => '1.0',
            'api_url' => 'http://lixingss.gicp.net:24294/cbhis/admin.php/Index/test',
        ],
        'qLTL8Acri52' => [
            'version' => '',
            'api_url' => 'http://58.19.245.66:9090',
        ],
    ],
];
