<?php

namespace wbarcovsky\yii2\request_docs\models\apiDocJs\Tags;

use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\Types\Context;
use wbarcovsky\yii2\request_docs\helpers\StringHelper;
use Webmozart\Assert\Assert;

class ApiDefine extends BaseTag implements StaticMethod
{
    /** @var string $name */
    protected $name = 'apiDefine';
    /** @var string $id Unique name for the block / value */
    protected $id;
    /** @var string $title A short title */
    protected $title = '';
    /** @var Description|null Description of the tag. */
    protected $description;

    /**
     * @param string $id Unique name for the block / value. Same name with different `@apiVersion` can be defined.
     * @param string $title A short title. Only used for named functions like `@apiPermission` or @apiParam (name).
     * @param string $description Detailed Description start at the next line, multiple lines can be used. Only used for named functions like `@apiPermission`.
     */
    public function __construct($id, $title = '', Description $description = null)
    {
        Assert::stringNotEmpty($id);

        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @param boolean $isTagName Флаг - вернут имя тега или уникальное имя.
     * @return string
     */
    public function getName($isTagName = true)
    {
        return $isTagName ? $this->name : $this->id;
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
    public static function create($body, DescriptionFactory $descriptionFactory = null, Context $context = null)
    {
        Assert::stringNotEmpty($body);
        Assert::notNull($descriptionFactory);

        $eol = StringHelper::detectEOL($body);
        $parts = preg_split('~(' . $eol . ')~u', $body);
        Assert::notEmpty($parts);
        $firstLine = array_shift($parts);
        $firstLineParts = preg_split('~(\s+)~Su', $firstLine, 2);

        $id = $firstLineParts[0];
        $title = empty($firstLineParts[1]) ? '' : $firstLineParts[1];
        $description = $descriptionFactory->create(implode("\n", $parts), $context);

        return new static($id, $title, $description);
    }


    /**
     * Returns a string representation for this tag.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->id . ' '
            . ($this->title ? ' ' . $this->title : '');
    }
}
