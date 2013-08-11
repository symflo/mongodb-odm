<?php

namespace Symflo\MongoDBODM;

use Symflo\MongoDBODM\Document\CollectionHandler;
use Symflo\MongoDBODM\Document\DocumentInterface;
use Symflo\MongoDBODM\Validator\ValidatorDocumentInterface;

/**
 * @author Florent Mondoloni
 */
class DocumentManager
{
    use \Symflo\MongoDBODM\Behavior\ListenerTrait;

    private $collection;
    private $normalizer;
    private $collectionHandler;
    private $collectionInstanced = array();

    /**
     * __construct
     * 
     * @param Connection                 $connection
     * @param CollectionHandler          $collectionHandler
     * @param ValidatorDocumentInterface $validatorDocument
     */
    public function __construct(Connection $connection, CollectionHandler $collectionHandler, ValidatorDocumentInterface $validatorDocument)
    {
        $this->connection        = $connection;
        $this->collectionHandler = $collectionHandler;
        $this->validatorDocument = $validatorDocument;
    }

    /**
     * getConnection
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * save
     * @param  array|DocumentInterface $document
     */
    public function save($document)
    {
        if (false === $this->validateDocument($document)) {
            return false;
        }

        $this->preSave($document);
        $this->collection = $this->findCollectionForDocument($document);

        if ($this->isNew($document)) {
            $this->create($document);
        } else {
            $this->update($document);
        }
        
        $this->postSave($document);

        return true;
    }

    /**
     * batchInsert
     * @param  array  $documents
     */
    public function batchInsert(array $documents)
    {
        if (count($documents) == 0) {
            return;
        }

        $documentsNormalize = array();
        $i = 0;
        foreach ($documents as $document) {
            if ($i == 0) {
                $this->collection = $this->findCollectionForDocument($document);    
            }
            $documentsNormalize[] = $this->getPropertiesToSave($document);
            ++$i;
        }

        $this->collection->batchInsert($documentsNormalize);
    }

    /**
     * remove
     * @param  DocumentInterface $document
     */
    public function remove(DocumentInterface $document)
    {
        $this->preRemove($document);
        $this->collection->remove(array('_id', $document->get_id()));
        $this->postRemove($document);
    }

    /**
     * create
     * @param  DocumentInterface $document
     */
    protected function create(DocumentInterface $document)
    {
        $this->preCreate($document);
        $properties = $this->getPropertiesToSave($document);
        $this->collection->save($properties);
        $document->set_id($properties['_id']);
        $this->postCreate($document);
    }

    /**
     * update
     * @param  DocumentInterface $document
     */
    protected function update(DocumentInterface $document)
    {
        $this->preUpdate($document);
        $this->collection->save($this->getPropertiesToSave($document));
        $this->postUpdate($document);
    }

    /**
     * push
     * @param  DocumentInterface $document       
     * @param  string            $property      
     * @param  DocumentInterface $documentToPush                           
     */
    public function push(DocumentInterface $document, $property, DocumentInterface $documentToPush)
    {
        $this->collection->update(
            array("_id" => $document->get_id()), 
            array('$push' => array($property => $this->getPropertiesToSave($documentToPush)))
        );
    }

    /**
     * getPropertiesToSave
     * @param  DocumentInterface $document
     * @return array
     */
    public function getPropertiesToSave(DocumentInterface $document)
    {
        if (method_exists($document, 'get_id') 
            && $this->isNew($document) 
            && null != $document->getId()) {
            $document->set_id($document->getId());
        }

        return $this->collectionHandler->getNormalizer()->normalize($document);
    }

    /**
     * isNew
     * @param  DocumentInterface $document
     * @return boolean 
     */
    private function isNew(DocumentInterface $document)
    {
        return (bool) (null === $document->get_id());
    }

    /**
     * getCollection
     * @param  string $collection
     * @return Symflo\MongoDBODM\Document\Collection
     */
    public function getCollection($collection)
    {
        if (array_key_exists($collection, $this->collectionInstanced)) {
            $this->collectionHandler->setDocumentManager($this);
            $this->collectionHandler->setCollection($this->collectionInstanced[$collection]);
            return $this->collectionHandler;
        }

        if (!$this->connection->getConfigurator()->supportCollection($collection)) {
            throw new \InvalidArgumentException(sprintf('Collection %s does not defined', $collection));
        }

        $configurator = $this->connection->getConfigurator();
        foreach ($configurator->getDocuments() as $documentOptions) {
            if ($documentOptions['collectionName'] == $collection) {
                $class = $documentOptions['collectionClass'];
                if (null === $class) {
                    $class = $configurator->getBaseMongoCollection();
                }

                $collectionObject = new $class($this->connection->getDb()->$collection, $documentOptions['class'], $this->collectionHandler);
                $this->collectionInstanced[$collection] = $collectionObject;

                $this->collectionHandler->setDocumentManager($this);
                $this->collectionHandler->setCollection($collectionObject);

                return $this->collectionHandler;
            }
        }
    }

    /**
     * findCollectionForDocument
     * 
     * @param  DocumentInterface $document
     * @return \MongoCollection
     */
    public function findCollectionForDocument(DocumentInterface $document)
    {
        $collection = $this->connection->getConfigurator()->getCollectionNameForDocument(get_class($document));
        return $this->connection->getDb()->$collection;
    }

    /**
     * getValidatorErrors
     * @return array
     */
    public function getValidatorErrors()
    {
        return $this->getValidatorDocument()->getErrors();
    }

    /**
     * getValidatorDocument
     * @return ValidatorDocument
     */
    public function getValidatorDocument()
    {
        return $this->validatorDocument;
    }

    /**
     * validateDocument
     * @param  DocumentInterface|array $document
     * @return boolean
     */
    private function validateDocument($document)
    {
        if (is_array($document)) {
            foreach ($document as $doc) {
                if (!$this->getValidatorDocument()->validate($doc)) {
                    return false;
                }

                if (false === $this->save($doc)) {
                    return false;
                }
            }

            return true;
        } else {
            if (!$this->getValidatorDocument()->validate($document)) {
                return false;
            }    
        }
    }
}