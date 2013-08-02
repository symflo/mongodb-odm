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
     */
    public function applyIndex()
    {
        foreach ($this->configurator->getDocuments() as $documentConfigs) {
            $collectionClass = $documentConfigs['collectionClass'];
            $collectionName  = $documentConfigs['collectionName'];

            if (null === $collectionClass && !method_exists($collectionClass, 'getIndexes')) {
                continue;
            }

            $indexes = $collectionClass::getIndexes();
            $collection = $this->documentManager->getCollection($collectionName);
            $collection->deleteIndexes();

            foreach ($indexes as $index) {
                $index = array_merge(array('options' => array()), $index);
                $this->validateIndex($index);

                $collection->ensureIndex($index['keys'], $index['options']);
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