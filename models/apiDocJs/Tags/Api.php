<?php

namespace wbarcovsky\yii2\request_docs\models\apiDocJs\Tags;

use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use Webmozart\Assert\Assert;

class Api extends BaseTag implements StaticMethod
{
    /** @var string $name */
    protected $name = 'api';
    /** @var string $method HTTP method */
    protected $method;
    /** @var string $path Request Path */
    protected $path;
    /** @var string $title A short title */
    protected $title = '';

    /**
     * @param string $method HTTP method
     * @param string $path Request Path
     * @param string $title A short title
     */
    public function __construct($method, $path, $title = '')
    {
        Assert::oneOf($method, ['{get}', '{head}', '{post}', '{put}', '{delete}', '{trace}', '{options}', '{connect}', '{patch}']);
        Assert::stringNotEmpty($path);

        preg_match('~\{(?<method>.+)\}~u', $method, $matches);
        $method = mb_convert_case($matches['method'], MB_CASE_UPPER);

        $this->method = $method;
        $this->path = $path;
        $this->title = $title;
    }

    /**
     * HTTP метод в верхнем регистре.
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public static function create($body)
    {
        Assert::stringNotEmpty($body);

        $parts = preg_split('~(\s+)~Su', $body, 3);
        Assert::greaterThanEq(count($parts), 2, 'method or path not set');
        $method = $parts[0];
        $path = $parts[1];
        $title = $parts[2] ? $parts[2] : '';

        return new static($method, $path, $title);
    }


    /**
     * Returns a string representation for this tag.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->method . ' '
            . $this->path
            . ($this->title ? ' ' . $this->title : '');
    }
}
