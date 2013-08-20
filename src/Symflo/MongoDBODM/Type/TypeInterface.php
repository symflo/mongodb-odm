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
     * @param Symflo\MongoDBODM\Document\DocumentInterface $document
     * @param array $property
     * @param array $propertyOptions
     * @return boolean
     */
    public function validate($value, $document, $property, $propertyOptions);

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