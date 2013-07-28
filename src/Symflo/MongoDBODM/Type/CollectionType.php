<?php

namespace Symflo\MongoDBODM\Type;

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
        $documentsArray = array();
        foreach ($value as $doc) {
            $documentsArray[] = $this->normalizer->denormalize($doc, $propertyOptions['reference']);
        }

        return $documentsArray;
    }
}