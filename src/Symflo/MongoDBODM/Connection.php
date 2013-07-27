<?php

namespace Symflo\MongoDBODM;

/**
 * Connection
 * @author Florent Mondoloni
 */
class Connection
{
    private $databases = array(); //databases possible
    private $db; //current database
    private $connection;
    private $mongoClient;
    private $configurator;

    /**
     * __construct
     * @param array $config
     */
    public function __construct(Configurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function init()
    {
        $this->mongoClient = new \MongoClient($this->configurator->getServer());
        $this->useDatabase($this->configurator->getDatabase());
    }

    /**
     * addDatabase
     * @param string $database
     */
    public function addDatabase($database)
    {
        $this->databases[] = $database;
    }

    /**
     * useDatabase
     * @param  string $db
     */
    public function useDatabase($db)
    {
        $this->db = $this->mongoClient->selectDB($db);
    }

    /**
     * getDb
     * @return \Cursor
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * getMongoClient
     * @return \MongoClient
     */
    public function getMongoClient()
    {
        return $this->mongoClient;
    }

    /**
     * getConfigurator
     * @return Configurator
     */
    public function getConfigurator()
    {
        return $this->configurator;
    }
}