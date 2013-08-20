<?php

namespace Symflo\MongoDBODM\Type;

/**
 * DateType
 * @author Florent Mondoloni
 */
class DateType implements TypeInterface
{
    /**
     * {% inheritdoc %}
     */
    public function validate($value, $document, $property, $propertyOptions)
    {
        return ($value instanceof \MongoDate);
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
        return $value;
    }
}