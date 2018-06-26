<?php

namespace wbarcovsky\yii2\request_docs\assets;

use yii\web\AssetBundle;

class DocsAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $css = [
        'css/bulma.min.css',
        'css/json-viewer.css',
        'css/style.css',
    ];

    public $js = [
        'js/jquery.js',
        'js/json-viewer.js',
        'js/showdown_1.8.6.min.js',
        'js/scripts.js',
    ];

    public $publishOptions = [
        'forceCopy' => true,
    ];
}
