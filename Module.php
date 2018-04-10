<?php
namespace wbarcovsky\yii2\request_docs;

use wbarcovsky\yii2\request_docs\commands\UrlResolverController;
use wbarcovsky\yii2\request_docs\components\RequestDocs;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'wbarcovsky\yii2\request_docs\controllers';

    public $defaultRoute = 'docs';

    public $title = 'API Documentation';

    /**
     * @var string
     */
    public $requestDocsComponent;

    public function init()
    {
        if (empty($this->requestDocsComponent)) {
            throw new \Exception("You must pass requestDocsComponent to request docs module");
        }
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'wbarcovsky\yii2\request_docs\commands';
        }
    }

    /**
     * @return RequestDocs
     */
    public function getRequestDocsComponent()
    {
        $component = $this->requestDocsComponent;
        return \Yii::$app->$component;
    }
}
