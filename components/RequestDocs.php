<?php

namespace wbarcovsky\yii2\request_docs\components;

use wbarcovsky\yii2\request_docs\helpers\StructureHelper;
use wbarcovsky\yii2\request_docs\models\DocRequest;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class RequestDocs extends Component
{
    public $storeFolder = '@app/runtime/docs';

    public $excludeParams = [];

    /**
     * @var DocRequest[]
     */
    protected $requests = null;

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $result
     * @return DocRequest
     */
    public function addRequest($method, $url, $params = [], $result = [])
    {
        $request = new DocRequest([
            'url' => $url,
            'method' => $method,
        ]);
        $request->addResult($result);
        $request->addParams($params);
        $this->requests[] = $request;
        return $this->$request;
    }

    public function storeRequests()
    {
        $path = \Yii::getAlias($this->storeFolder);
        if (!is_writeable($path)) {
            throw new \Exception("Path '{$path}' is not writeable'");
        }
        foreach ($this->requests as $request) {
            if (!empty($request->hash) && $request->getDataHash() === $request->hash) {
                continue;
            }
            $mergeParams = [];
            foreach ($request->getParams() as $param) {
                $mergeParams = ArrayHelper::merge($mergeParams, $param);
            }
            $mergeResult = [];
            foreach ($request->getResult() as $result) {
                $mergeResult = ArrayHelper::merge($mergeResult, $result);
            }
            $shortInfo = [
                'method' => $request->method,
                'url' => $request->url,
                'params' => StructureHelper::getStructure($mergeParams),
                'result' => StructureHelper::getStructure($mergeResult),
            ];
            // Store short info in .json file
            $shortPath = $this->getShortInfoPath($request);
            $this->createDir(dirname($shortPath));
            file_put_contents($shortPath, StructureHelper::jsonPrettyPrint($shortInfo, false));

            // Store full info in zip archive
            $request->hash = $request->getDataHash();
            $fillInfoPath = $this->getFullInfoPath($request);
            $this->createDir(dirname($fillInfoPath));
            //file_put_contents("zip://{$fillInfoPath}#info.json", json_encode($request));
        }
    }

    /**
     * @param DocRequest $request
     * @return string
     */
    protected function getShortInfoPath($request)
    {
        return \Yii::getAlias($this->storeFolder) . '/' . $request->method . '__' . $this->getUrlPath($request->url) . '.json';
    }

    /**
     * @param DocRequest $request
     * @return string
     */
    protected function getFullInfoPath($request)
    {
        return \Yii::getAlias($this->storeFolder) . '/zip/' . $request->method . '__' . $this->getUrlPath($request->url) . '.zip';
    }

    protected function getUrlPath($url)
    {
        return str_replace(':id', 'id', str_replace('/', '-', $url));
    }

    protected function loadRequests()
    {
        // TODO!
        $this->requests = [];
    }

    protected function createDir($folder)
    {
        if (!file_exists(dirname($folder))) {
            self::createDir(dirname($folder));
        }
        if (file_exists($folder)) {
            return;
        }
        mkdir($folder);
        chmod($folder, 0777);
    }

}