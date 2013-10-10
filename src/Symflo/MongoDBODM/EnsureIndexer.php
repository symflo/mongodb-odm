<?php

namespace Symflo\MongoDBODM;

/**
 * EnsureIndexer
 * @author Florent Mondoloni
 */
class EnsureIndexer
{
    private $configurator;
    private $documentManager;

    /**
     * __construct
     * @param DocumentManager $documentManager
     * @param Configurator $configurator
     */
    public function __construct($documentManager, $configurator)
    {
        $this->configurator    = $configurator;
        $this->documentManager = $documentManager;
    }

    /**
     * applyIndex
     * @param  boolean $forceDelete
     */
    public function applyIndex($forceDelete = false)
    {
        foreach ($this->configurator->getDocuments() as $documentConfigs) {
            $collectionClass = $documentConfigs['collectionClass'];
            $collectionName  = $documentConfigs['collectionName'];

            if (null === $collectionClass || !method_exists($collectionClass, 'getIndexes')) {
                continue;
            }

            $indexes = $collectionClass::getIndexes();
            $collection = $this->documentManager->getCollection($collectionName);

            $indexesKeys = array();
            foreach ($indexes as $index) {
                $index = array_merge(array('options' => array()), $index);
                $this->validateIndex($index);

                $collection->ensureIndex($index['keys'], $index['options']);
                $indexesKeys[] = $index['keys'];
            }

            if ($forceDelete) {
                $this->deleteIndexes($collection, $indexesKeys);
            }
        }
    }

    /**
     * deleteIndexes
     * @param  $collection 
     * @param  array $indexesKeys 
     */
    public function deleteIndexes($collection, array $indexesKeys)
    {
        foreach ($collection->getIndexInfo() as $indexInfo) {
            if (!in_array($indexInfo['key'], $indexesKeys)) {
                $collection->deleteIndex($indexInfo['key']);
            }
        }
    }

    /**
     * validateIndex
     * @param  array  $index
     * @throws Exception If Index is not valid
     */
    private function validateIndex(array $index)
    {
        if (!array_key_exists('keys', $index)) {
            throw new \Exception("Index is not valid. Key 'keys' is missing");
        }
    }
}