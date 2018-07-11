<?php

Yii::setAlias('wbarcovsky\yii2\request_docs', dirname(__DIR__));

return [
    'id' => 'basic',
    'language' => 'en-EN',
    'basePath' => dirname(__DIR__),
    'modules' => [
        'docs' => [
            'class' => 'wbarcovsky\yii2\request_docs\Module',
            'requestDocsComponent' => 'docs'
        ],
    ],
    'components' => [
        'docs' => [
            'class' => 'wbarcovsky\yii2\request_docs\components\RequestDocs',
            'storeFolder' => __DIR__ . '/data/',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'assetsManager' => [
            'class' => 'yii\web\AssetManager',
            'forceCopy' => true,
        ]
    ],
    'defaultRoute' => 'docs',
];
