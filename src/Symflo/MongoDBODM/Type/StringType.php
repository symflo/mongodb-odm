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
    public function validate($value)
    {
        return (is_scalar($value) || (is_object($value) && is_callable(array($value, '__toString'))));
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
        return 'Value is not string';
    }

    /**
     * {% inheritdoc %}
     */
    public function hydrate($value, $propertyOptions)
    {
        return $value;
    }
}