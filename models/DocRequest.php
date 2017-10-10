<?php

namespace wbarcovsky\yii2\request_docs\models;

use wbarcovsky\yii2\request_docs\helpers\StructureHelper;
use yii\base\Model;

/**
 * Class DocRequest
 * @property string $url
 * @property string $method
 * @property array $params
 * @property array $result
 */
class DocRequest extends Model
{
    protected static $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    public $hash;

    public $title = '';

    public $description;

    protected $_method;

    protected $_params = [];

    protected $_result = [];

    protected $_url;

    public function fields()
    {
        return [
            'hash',
            'title',
            'description',
            'method',
            'url',
            'params',
            'result',
        ];
    }

    public function attributes()
    {
        return $this->fields();
    }

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

    public function getParams()
    {
        return $this->_params;
    }

    public function setParams($params)
    {
        $this->_params = $params;
    }
    public function addParams($params)
    {
        if (!empty($params)) {
            $this->_params[] = $params;
        }
    }


    public function getResult()
    {
        return $this->_result;
    }

    public function setResult($result)
    {
        $this->_result = $result;
    }
    public function addResult($result)
    {
        if (!empty($result)) {
            $this->_result[] = $result;
        }
    }

    public function getMethodHash()
    {
        return substr(md5($this->url . $this->method . $this->title), 0, 6);
    }

    public function getDataHash()
    {
        return substr(StructureHelper::getStructureHash($this->params), 0, 5) .
            substr(StructureHelper::getStructureHash($this->result), 0, 5);
    }
}