<?php
namespace wbarcovsky\yii2\request_docs;

use wbarcovsky\yii2\request_docs\components\RequestDocs;
use yii\base\BootstrapInterface;
use yii\web\Application;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'wbarcovsky\yii2\request_docs\controllers';

    public $docUrl = 'docs';

    /**
     * @var RequestDocs
     */
    public $requestDocsComponent;

    public function bootstrap($app)
    {
        if (empty($this->requestDocsService)) {
            throw new \Exception("You must pass requestDocsComponent to request docs module");
        }
        if ($app instanceof Application) {
            $app->getUrlManager()->addRules([
                $this->docUrl => $this->id . '/default/index',
                $this->docUrl . '/<controller:[\w\-]+>/<action:[\w\-]+>' => $this->id . '/<controller>/<action>',
            ], false);
        }
    }
}