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
    public function validate($value)
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
}