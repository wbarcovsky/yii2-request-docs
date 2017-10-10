<?php

namespace wbarcovsky\yii2\request_docs\models;

use wbarcovsky\yii2\request_docs\helpers\StructureHelper;
use yii\base\Object;

/**
 * Class DocRequest
 * @property string $url
 * @property string $method
 * @property array $params
 * @property array $result
 */
class DocRequest extends Object
{
    protected static $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    public $hash;

    public $description = null;

    protected $_method;

    protected $_params = [];

    protected $_result = [];

    protected $_url;

    public function getUrl()
    {
        return $this->_url;
    }

    public function setUrl($url)
    {
        $this->_url = self::normalizeUrl($url);
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function setMethod($method)
    {
        if (empty($method)) {
            throw new \Exception("Http method cannot be empty!");
        }
        if (!in_array($method, self::$allowedMethods)) {
            throw new \Exception("Wrong http header - {$method}");
        }
        $this->_method = $method;
    }

    public static function normalizeUrl($url)
    {
        return preg_replace('/\/(\d+)$/', '/:id', trim(trim($url), '/'));
    }

    public function getDataHash()
    {
        return substr(StructureHelper::getStructureHash($this->params), 0, 5) .
            substr(StructureHelper::getStructureHash($this->result), 0, 5);
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function addParams($params)
    {
        $this->_params[] = $params;
    }

    public function getResult()
    {
        return $this->_result;
    }

    public function addResult($result)
    {
        $this->_result[] = $result;
    }
}