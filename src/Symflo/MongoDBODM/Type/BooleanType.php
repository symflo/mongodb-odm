<?php

namespace Symflo\MongoDBODM\Type;

/**
 * BooleanType
 * @author Florent Mondoloni
 */
class BooleanType implements TypeInterface
{
    /**
     * {% inheritdoc %}
     */
    public function validate($value)
    {
        return is_bool($value);
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
        return 'Value is not boolean';
    }

    /**
     * {% inheritdoc %}
     */
    public function hydrate($value, $propertyOptions)
    {
        return $value;
    }
}