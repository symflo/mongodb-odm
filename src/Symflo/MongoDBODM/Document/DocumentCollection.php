<?php

namespace Symflo\MongoDBODM\Document;

use Countable;
use IteratorAggregate;
use ArrayAccess;

/**
 * DocumentCollection document Object Collection.
 * 
 * @author Florent Mondoloni
 */
class DocumentCollection implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * An array containing the entries of this collection.
     */
    private $documents;

    /**
     * __construct
     *
     * @param array $documents
     */
    public function __construct(array $documents = array())
    {
        $this->documents = $documents;
    }

    /**
     * getIterator
     * Gets an iterator for iterating over the documents in the collection.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->documents);
    }

    /**
     * getdocuments
     * 
     * @return array()
     */
    public function getdocuments()
    {
        return $this->documents;
    }

    /**
     * updateCurrent
     * 
     * @param  Symflo\MongoDBODM\Document\DocumentInterface $currentdocument
     */
    public function updateCurrent(DocumentInterface $currentdocument)
    {
        foreach ($this as $document) {
            $currentdocument->setIsCurrent($currentdocument === $document);
        }
    }

    /**
     * add
     * @param Symflo\MongoDBODM\Document\DocumentInterface $document
     */
    public function add(DocumentInterface $document)
    {
        $this->documents[] = $document;
    }

    /**
     * setCurrentKey 
     * Moves the internal iterator position to the key document.
     * 
     * @param string $key
     */
    public function setCurrentKey($key)
    {
        $this->documents[$key];
    }

    /**
     * current 
     * Gets the document of the collection at the current internal iterator position.
     *
     * @return Symflo\MongoDBODM\Document\DocumentInterface
     */
    public function current()
    {
        return current($this->documents);
    }

    /**
     * next 
     * Moves the internal iterator position to the next document.
     *
     * @return Symflo\MongoDBODM\Document\DocumentInterface
     */
    public function next()
    {
        return next($this->documents);
    }

    /**
     * prev 
     * Moves the internal iterator position to the prev document.
     *
     * @return Symflo\MongoDBODM\Document\DocumentInterface
     */
    public function prev()
    {
        return prev($this->documents);
    }

    /**
     * first
     * 
     * @return Symflo\MongoDBODM\Document\DocumentInterface
     */
    public function first()
    {
        return reset($this->documents);
    }

    /**
     * last
     * 
     * @return Symflo\MongoDBODM\Document\DocumentInterface
     */
    public function last()
    {
        return end($this->documents);
    }

    /**
     * containsKey
     * @param  string $key
     * @return boolean
     */
    public function containsKey($key)
    {
        return isset($this->documents[$key]);
    }

    /**
     * count 
     * Implementation of the Countable interface.
     *
     * @return integer The number of documents in the collection.
     */
    public function count()
    {
        return count($this->documents);
    }

    /**
     * remove
     * Removes an document with a specific key/index from the collection.
     *
     * @param mixed $key
     * @return mixed The removed document or NULL, if no document exists for the given key.
     */
    public function remove($key)
    {
        if (isset($this->documents[$key])) {
            $removed = $this->documents[$key];
            unset($this->documents[$key]);
            
            return $removed;
        }

        return null;
    }

    /**
     * offsetExists
     * ArrayAccess implementation of offsetExists()
     *
     * @see containsKey()
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * offsetGet
     * ArrayAccess implementation of offsetGet()
     *
     * @see get()
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * ArrayAccess implementation of offsetGet()
     *
     * @see add()
     * @see set()
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            return $this->add($value);
        }
        return $this->set($offset, $value);
    }

    /**
     * ArrayAccess implementation of offsetUnset()
     *
     * @see remove()
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * isEmpty
     * @return boolean
     */
    public function isEmpty()
    {
        return (bool) !$this->documents;
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '@' . spl_object_hash($this);
    }

    /**
     * Clears the collection.
     */
    public function clear()
    {
        $this->documents = array();
    }

    /**
     * __destruct delete all private attributes
     */
    public function __destruct()
    {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }
}
