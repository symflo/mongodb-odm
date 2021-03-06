<?php

namespace Symflo\MongoDBODM\Type;

use Symflo\MongoDBODM\Document\DocumentCollection;

/**
 * EmbeddedCollectionType
 * @author Florent Mondoloni
 */
class EmbeddedCollectionType implements TypeInterface
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
    public function validate($value, $document, $property, $propertyOptions)
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
        $documentCollection = new DocumentCollection();
        foreach ($value as $doc) {
            $reference = $this->configurator->getClassForDocumentName($propertyOptions['reference']);
            $documentCollection->add($this->normalizer->denormalize($doc, $reference));
        }

        return $documentCollection;
    }
}