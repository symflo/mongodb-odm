<?php

namespace Symflo\MongoDBODM\Type;

/**
 * StringType
 * @author Florent Mondoloni
 */
class StringType implements TypeInterface
{
    /**
     * {% inheritdoc %}
     */
    public function validate($value, $document, $property, $propertyOptions)
    {
        return (is_string($value) || (is_object($value) && is_callable(array($value, '__toString'))));
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
        return 'Value is not a string';
    }

    /**
     * {% inheritdoc %}
     */
    public function hydrate($value, $propertyOptions)
    {
        return $value;
    }
}