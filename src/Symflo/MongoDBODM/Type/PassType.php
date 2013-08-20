<?php

namespace Symflo\MongoDBODM\Type;

/**
 * PassType
 * @author Florent Mondoloni
 */
class PassType implements TypeInterface
{
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
        return $value;
    }
}