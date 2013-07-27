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
        $m->getConfigurator()->setConfig($config);
        $m->init();

        $normalizer = new ODMNormalizer();
        $collectionHandler = new CollectionHandler($normalizer);
        $validatorDocument = new ValidatorDocument($configurator);

        return new DocumentManager($m, $collectionHandler, $validatorDocument);
    }
}