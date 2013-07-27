<?php

namespace Symflo\MongoDBODM\Document;

/**
 *
 * DocumentInterface
 *
 * @author Florent Mondoloni
 */
interface DocumentInterface
{
    /**
     * getProperties.
     * 
     * @return array
     */
    public function getProperties();

    /**
     * getMongoId.
     * 
     * @return \MongoId
     */
    public function getMongoId();
}