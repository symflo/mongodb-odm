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
    public function validate($value)
    {
        return true;
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
    }
}