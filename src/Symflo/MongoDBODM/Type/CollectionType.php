<?php

namespace Symflo\MongoDBODM\Type;

use Symflo\MongoDBODM\Document\DocumentCollection;

/**
 * CollectionType
 * @author Florent Mondoloni
 */
class CollectionType implements TypeInterface
{   
    private $normalizer;

    /**
     * __construct
     * @param $normalizer
     */
    public function __construct($normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * {% inheritdoc %}
     */
    public function validate($value)
    {
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
    }

    /**
     * {% inheritdoc %}
     */
    public function hydrate($value, $propertyOptions)
    {
        $documentCollection = new DocumentCollection();
        foreach ($value as $doc) {
            $documentCollection->add($this->normalizer->denormalize($doc, $propertyOptions['reference']));
        }

        return $documentCollection;
    }
}