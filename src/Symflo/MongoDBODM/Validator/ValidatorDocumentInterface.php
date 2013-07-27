<?php

namespace Symflo\MongoDBODM\Validator;

use Symflo\MongoDBODM\Document\DocumentInterface;

/**
 * ValidatorDocumentInterface
 * @author Florent Mondoloni
 */
interface ValidatorDocumentInterface
{
    /**
     * validate
     * Validate all document type.
     * @return boolean
     */
    public function validate(DocumentInterface $document);

    /**
     * getErrors
     * Return errors during validate
     * @return array
     */
    public function getErrors();
}