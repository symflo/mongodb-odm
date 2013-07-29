<?php

namespace Symflo\MongoDBODM\Behavior;

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
        $this->executeListenerIfExist($document, 'preSave');
    }

    /**
     * postSave
     * @param  DocumentInterface $document
     */
    public function postSave(DocumentInterface $document)
    {
        $this->executeListenerIfExist($document, 'postSave');
    }

    /**
     * preCreate
     * @param  DocumentInterface $document
     */
    public function preCreate(DocumentInterface $document)
    {
        $this->executeListenerIfExist($document, 'preCreate');
    }

    /**
     * postCreate
     * @param  DocumentInterface $document
     */
    public function postCreate(DocumentInterface $document)
    {
        $this->executeListenerIfExist($document, 'postCreate');
    }

    /**
     * preUpdate
     * @param  DocumentInterface $document
     */
    public function preUpdate(DocumentInterface $document)
    {
        $this->executeListenerIfExist($document, 'preUpdate');
    }

    /**
     * postUpdate
     * @param  DocumentInterface $document
     */
    public function postUpdate(DocumentInterface $document)
    {
        $this->executeListenerIfExist($document, 'postUpdate');
    }

    /**
     * preRemove
     * @param  DocumentInterface $document
     */
    public function preRemove(DocumentInterface $document)
    {
        $this->executeListenerIfExist($document, 'preRemove');
    }

    /**
     * postRemove
     * @param  DocumentInterface $document
     */
    public function postRemove(DocumentInterface $document)
    {
        $this->executeListenerIfExist($document, 'postRemove');
    }

    /**
     * executeListenerIfExist
     * @param  DocumentInterface $document
     * @param  string            $listener
     */
    protected function executeListenerIfExist(DocumentInterface $document, $listener)
    {
        if (method_exists($document, $listener)) {
            $document->$listener();
        }       
    }
}