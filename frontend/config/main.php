<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
	'language' => 'zh-CN',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
	        'class'=>'yii\web\User',//uesr组件的实现类
	        'identityClass' => \frontend\models\Member::className(),//设置IdentityClass的实现类
	        'enableAutoLogin' => true,
	        'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
	        'loginUrl'=>['member/index'],//设置 后台app 的登录页面的地址
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        //美化url
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
	    //    'suffix' => '.html',
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];
