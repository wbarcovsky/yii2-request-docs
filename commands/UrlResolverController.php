<?php

namespace wbarcovsky\yii2\request_docs\commands;

use Yii;
use wbarcovsky\yii2\request_docs\helpers\UrlResolver;

class UrlResolverController extends \yii\console\Controller
{
    public function actionRoute($moduleId, $url, $httpMethod = 'GET')
    {
        $executor = Yii::getAlias('@app/yii');
        $moduleId = escapeshellcmd($moduleId);
        $cmd = "$executor {$moduleId}/url-resolver/sandbox-command-runner " . escapeshellarg($url) . ' ' . escapeshellarg($httpMethod);
        $urlUnfo = shell_exec($cmd);
        if (preg_match('~^Controller:(?<controller>.*),action:(?<action>.*)$~iu', $urlUnfo, $matches)) {
            return [$matches['controller'], $matches['action']];
        }
        return [];
    }

    public function actionSandboxCommandRunner($url, $httpMethod = 'GET')
    {
        list($controller, $actionMethodName) = UrlResolver::getRoute($url, $httpMethod);
        $controllerName = get_class($controller);
        echo "Controller:{$controllerName},action:{$actionMethodName}\n";
    }
}
