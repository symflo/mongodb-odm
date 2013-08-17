<?php

namespace Symflo\MongoDBODM;

use Symflo\MongoDBODM\Connection;
use Symflo\MongoDBODM\Normalizer\ODMNormalizer;
use Symflo\MongoDBODM\Document\CollectionHandler;
use Symflo\MongoDBODM\DocumentManager;
use Symflo\MongoDBODM\Validator\ValidatorDocument;
use Symflo\MongoDBODM\Configurator;

/**
 * DocumentManagerFactory
 * Prefer DIC as this factory 
 * @author Florent Mondoloni
 */
class DocumentManagerFactory
{
    /**
     * create
     * @param  array $config
     * @return DocumentManager
     */
    public static function create($config)
    {
        $configurator = new Configurator();
        $m = new Connection($configurator);

        $normalizer = new ODMNormalizer();
        $collectionHandler = new CollectionHandler($normalizer, $configurator);
        $validatorDocument = new ValidatorDocument($configurator);

        $documentManager = new DocumentManager($m, $collectionHandler, $validatorDocument);
        $indexer = new EnsureIndexer($documentManager, $configurator);

        //type services
        $types = array(
            'date'               => new \Symflo\MongoDBODM\Type\DateType(),
            'string'             => new \Symflo\MongoDBODM\Type\StringType(),
            'integer'            => new \Symflo\MongoDBODM\Type\IntegerType(),
            'boolean'            => new \Symflo\MongoDBODM\Type\BooleanType(),
            'pass'               => new \Symflo\MongoDBODM\Type\PassType(),
            'manualReference'    => new \Symflo\MongoDBODM\Type\ManualReferenceType($documentManager),
            'manualReferences'   => new \Symflo\MongoDBODM\Type\ManualReferencesType($documentManager),
            'embeddedOne'        => new \Symflo\MongoDBODM\Type\EmbeddedOneType($configurator, $normalizer),
            'embeddedCollection' => new \Symflo\MongoDBODM\Type\EmbeddedCollectionType($configurator, $normalizer)
        );

        $documentManager->getConnection()->getConfigurator()->setDefaultTypes($types);
        $documentManager->getConnection()->getConfigurator()->setConfig($config);
        $documentManager->getConnection()->init();

        return array($indexer, $documentManager);
    }
}