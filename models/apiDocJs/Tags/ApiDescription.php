<?php

namespace wbarcovsky\yii2\request_docs\models\apiDocJs\Tags;

use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\Types\Context;
use wbarcovsky\yii2\request_docs\helpers\StringHelper;
use Webmozart\Assert\Assert;

class ApiDescription extends BaseTag implements StaticMethod
{
    /** @var string $name */
    protected $name = 'apiDescription';
    /** @var Description|null $text Multiline description text. */
    protected $text;

    /**
     * @param Description $text Multiline description text.
     */
    public function __construct(Description $text)
    {
        $this->text = $text;
    }

    /**
     * @return Description|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * {@inheritdoc}
     */
    public static function create($body, DescriptionFactory $descriptionFactory = null, Context $context = null)
    {
        Assert::stringNotEmpty($body);
        Assert::notNull($descriptionFactory);
        return new static($descriptionFactory->create($body, $context));
    }


    /**
     * Returns a string representation for this tag.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }
}
