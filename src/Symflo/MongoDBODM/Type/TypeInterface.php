<?php

namespace Symflo\MongoDBODM\Type;

/**
 * TypeInterface
 * @author Florent Mondoloni
 */
interface TypeInterface
{
    /**
     * validate
     * check insert value in document.
     * @param mixed $value
     * @return boolean
     */
    public function validate($value);

    /**
     * getError
     * @return string
     */
    public function getError();
}