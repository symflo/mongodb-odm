<?php

namespace Symflo\MongoDBODM\Type;

use Symflo\MongoDBODM\Document\DocumentCollection;

/**
 * @author Florent Mondoloni
 */
trait ManualReferenceTrait
{
    protected $reference;

    /**
     * {% inheritdoc %}
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * {% inheritdoc %}
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * validateReference
     * @param  array|Symflo\MongoDBODM\Document\DocumentCollection $values
     * @param  array $propertyOptions
     * @return boolean
     */
    private function validateReference($values, $propertyOptions)
    {
        $configurator = $this->getConfigurator();
        $class = $configurator->getClassForDocumentName($propertyOptions['reference']);
        $object = new $class();

        if (array_key_exists('id', $object->getProperties())) {
            $type = $configurator->getTypeForName($object->getProperties()['id']['type']);

            if (is_array($values) || $values instanceof DocumentCollection) {
                foreach ($values as $value) {
                    if (false === $type->validate($value, null, 'id', $object->getProperties()['id'])) {
                        return false;
                    }
                }
            }
        } else {
            foreach ($values as $value) {
                if (!$value instanceof \MongoId) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * getConfigurator
     * @return Symflo\MongoDBODM\Configurator
     */
    private function getConfigurator()
    {
        return $this->documentManager->getConnection()->getConfigurator();
    }
}