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
        return is_string($value);
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
        return 'Value is not string';
    }
}