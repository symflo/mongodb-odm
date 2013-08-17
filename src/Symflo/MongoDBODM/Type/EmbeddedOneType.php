<?php

namespace Symflo\MongoDBODM\Type;

use Symflo\MongoDBODM\Document\DocumentCollection;

/**
 * EmbeddedOneType
 * @author Florent Mondoloni
 */
class EmbeddedOneType implements TypeInterface
{   
    private $normalizer;
    private $configurator;

    /**
     * __construct
     * @param $configurator
     * @param $normalizer
     */
    public function __construct($configurator, $normalizer)
    {
        $this->configurator = $configurator;
        $this->normalizer   = $normalizer;
    }

    /**
     * {% inheritdoc %}
     */
    public function validate($value)
    {
        return true;
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
        return '';
    }

    /**
     * {% inheritdoc %}
     */
    public function hydrate($value, $propertyOptions)
    {
        if (false === $propertyOptions['reference']) {
            return $value;
        }

        $reference = $this->configurator->getClassForDocumentName($propertyOptions['reference']);
        return $this->normalizer->denormalize($value, $reference);
    }
}