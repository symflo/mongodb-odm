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

    /**
     * hydrate
     * @param  mixed $value
     * @param  array $propertyOptions
     * @return mixed
     */
    public function hydrate($value, $propertyOptions);
}