<?php

namespace wbarcovsky\yii2\request_docs\controllers;

use wbarcovsky\yii2\request_docs\Module;
use yii\web\Controller;

class DocsController extends Controller
{
    public function actionIndex()
    {
        $data = [];
        $data['search'] = \Yii::$app->request->getQueryParam('search');
        $data['requests'] = Module::getInstance()->getRequestDocsComponent()->loadShortInfo();
        usort($data['requests'], function ($a, $b) {
            return strcmp($a['url'], $b['url']);
        });
        // Search by selected request
        $getKeys = array_keys($_GET);
        $selectedRequest = isset($getKeys[0]) && $getKeys[0] !== 'search' ? $getKeys[0] : null;
        if (!empty($selectedRequest)) {
            list ($method, $url) = explode('_', $selectedRequest);
            if (!empty($method) && !empty($url)) {
                foreach ($data['requests'] as $key => $request) {
                    if ($request['method'] !== $method || $request['url'] !== $url) {
                        unset($data['requests'][$key]);
                    }
                }
            }
        }
        // Search requests by url or title
        if (!empty($data['search']) && empty($selectedRequest)) {
            $parts = explode(' ', trim($data['search']));
            foreach ($data['requests'] as $key => $request) {
                $remove = true;
                foreach ($parts as $part) {
                    if (mb_stripos($request['url'], $part) !== false) {
                        $remove = false;
                    }
                    if (mb_stripos($request['title'], $part) !== false) {
                        $remove = false;
                    }
                }
                if ($remove) {
                    unset($data['requests'][$key]);
                }
            }
        }
        $data['title'] = Module::getInstance()->title;
        $this->layout = false;
        return $this->render('/docs', $data);
    }

    public function actionFullInfo()
    {
        $hash = \Yii::$app->request->getQueryParam('hash');
        $request = Module::getInstance()->getRequestDocsComponent()->loadRequestByHash($hash);
        return json_encode($request->toArray());
    }
}