<?php

namespace Symflo\MongoDBODM\Document;

/**
 * @author Florent Mondoloni
 */
class Collection
{
    protected $mongoCollection;
    protected $documentClass;

    /**
     * __construct
     * @param \MongoCollection $mongoCollection
     * @param string $documentClass
     */
    public function __construct($mongoCollection, $documentClass, $collectionHandler)
    {
        $this->mongoCollection   = $mongoCollection;
        $this->documentClass     = $documentClass;
        $this->collectionHandler = $collectionHandler;
    }

    /**
     * Get CollectionHandler.
     *
     * @return type CollectionHandler value
     */
    public function getCollectionHandler()
    {
        return $this->collectionHandler;
    }

    /**
     * getDocumentClass
     * @return string
     */
    public function getDocumentClass()
    {
        return $this->documentClass;
    }

    /**
     * __call
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->mongoCollection, $name), $args);
    }
}