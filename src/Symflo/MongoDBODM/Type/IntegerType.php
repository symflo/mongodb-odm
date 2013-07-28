<?php

namespace Symflo\MongoDBODM\Type;

/**
 * IntegerType
 * @author Florent Mondoloni
 */
class IntegerType implements TypeInterface
{
    /**
     * {% inheritdoc %}
     */
    public function validate($value)
    {
        return is_numeric($value);
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
        return 'Value is not integer';
    }

    /**
     * {% inheritdoc %}
     */
    public function hydrate($value, $propertyOptions)
    {
        return $value;
    }
}