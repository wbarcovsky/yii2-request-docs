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

    public $autoLoadRequests = true;

    /**
     * @var DocRequest[]
     */
    protected $requests = null;

    public function init()
    {
        parent::init();
        if ($this->autoLoadRequests) {
            $this->loadRequests();
        }
    }

    /**
     * @param string $method
     * @param string $url
     * @param string $title
     * @param array $params
     * @param array $result
     * @return DocRequest
     */
    public function addRequest($method, $url, $title = '', $params = [], $result = [])
    {
        $request = new DocRequest([
            'url' => $url,
            'method' => $method,
            'title' => $title,
        ]);
        $hash = $request->getMethodHash();
        if (isset($this->requests[$hash])) {
            $this->requests[$hash]->addParams($params);
            $this->requests[$hash]->addResult($result);
        } else {
            $request->addResult($result);
            $request->addParams($params);
            $this->requests[$hash] = $request;
        }
        return $this->requests[$hash];
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
                'title' => $request->title,
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
            $zip = new \ZipArchive();
            $zip->open($fillInfoPath, \ZipArchive::CREATE);
            $zip->addFromString('data.json', json_encode($request->toArray()));
            $zip->close();
        }
    }

    /**
     * @param DocRequest $request
     * @return string
     */
    protected function getShortInfoPath($request)
    {
        return "{$this->folder()}/{$request->method}__{$this->getUrlPath($request->url)}.{$request->getMethodHash()}.json";
    }

    /**
     * @param DocRequest $request
     * @return string
     * @internal param bool $zip
     */
    protected function getFullInfoPath($request)
    {
        return "{$this->folder(true)}/{$request->method}__{$this->getUrlPath($request->url)}.{$request->getMethodHash()}.zip";
    }

    protected function folder($fullInfo = false)
    {
        return \Yii::getAlias($this->storeFolder) . ($fullInfo ? '/full_info' : '');
    }

    protected function getUrlPath($url)
    {
        return str_replace(':id', 'id', str_replace('/', '-', $url));
    }

    public function loadRequests()
    {
        $this->requests = [];
        $files = glob($this->folder(true) . '/*');
        foreach ($files as $file) {
            if (is_file($file) && $file) {
                $request = new DocRequest();
                $data = json_decode(file_get_contents("zip://{$file}#data.json"), true);
                if (isset($data['url'])) {
                    $request->setAttributes($data, false);
                    $this->requests[] = $request;
                }
            }
        }
    }

    /**
     * @param $hash
     * @return null|DocRequest
     */
    public function loadRequestByHash($hash)
    {
        $files = glob($this->folder(true) . '/*');
        foreach ($files as $file) {
            if (is_file($file) && $file) {
                $array = explode('.', $file);
                $hashFile = $array[count($array) - 2];
                if ($hashFile === $hash) {
                    $request = new DocRequest();
                    $data = json_decode(file_get_contents("zip://{$file}#data.json"), true);
                    $request->setAttributes($data, false);
                    return $request;
                }
            }
        }
        return null;
    }

    public function loadShortInfo()
    {
        $result = [];
        $files = glob($this->folder() . '/*');
        foreach ($files as $file) {
            if (is_file($file) && $file) {
                $data = json_decode(file_get_contents($file), true);
                $array = explode('.', $file);
                $hash = $array[count($array) - 2];
                if (isset($data['url']) && !empty($hash)) {
                    $result[$hash] = $data;
                    $result[$hash]['hash'] = $hash;
                }
            }
        }
        return $result;
    }

    public function getRequests()
    {
        return $this->requests;
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