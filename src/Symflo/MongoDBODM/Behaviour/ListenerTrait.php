<?php

namespace Symflo\MongoDBODM\Behaviour;

use Symflo\MongoDBODM\Document\DocumentInterface;

/**
 * @author Florent Mondoloni
 */
trait ListenerTrait
{
    /**
     * preSave
     * @param  DocumentInterface $document
     */
    public function preSave(DocumentInterface $document)
    {
    }

    /**
     * postSave
     * @param  DocumentInterface $document
     */
    public function postSave(DocumentInterface $document)
    {
    }

    /**
     * preCreate
     * @param  DocumentInterface $document
     */
    public function preCreate(DocumentInterface $document)
    {
    }

    /**
     * postCreate
     * @param  DocumentInterface $document
     */
    public function postCreate(DocumentInterface $document)
    {
    }

    /**
     * preUpdate
     * @param  DocumentInterface $document
     */
    public function preUpdate(DocumentInterface $document)
    {
    }

    /**
     * postUpdate
     * @param  DocumentInterface $document
     */
    public function postUpdate(DocumentInterface $document)
    {
    }

    /**
     * preRemove
     * @param  DocumentInterface $document
     */
    public function preRemove(DocumentInterface $document)
    {
    }

    /**
     * postRemove
     * @param  DocumentInterface $document
     */
    public function postRemove(DocumentInterface $document)
    {
    }
}