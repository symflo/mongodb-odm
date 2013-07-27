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
    public function getProperties();
    public function getMongoId();
}