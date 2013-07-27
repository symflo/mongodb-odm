<?php

namespace Symflo\MongoDBODM\Type;

/**
 * TypeInterface
 * @author Florent Mondoloni
 */
interface ManualReferenceTypeInterface
{
    /**
     * setReference
     * @param string class document name ref
     */
    public function setReference($reference);

    /**
     * getReference
     * @return string
     */
    public function getReference();    
}