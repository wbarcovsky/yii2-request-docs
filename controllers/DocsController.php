<?php

namespace wbarcovsky\yii2\request_docs\controllers;

use wbarcovsky\yii2\request_docs\Module;
use yii\web\Controller;

class DocsController extends Controller
{
    public function actionIndex()
    {
        $requests = Module::getInstance()->getRequestDocsComponent()->getRequests();
        var_dump($requests);
    }
}