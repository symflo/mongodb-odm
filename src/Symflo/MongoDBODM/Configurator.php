<?php

namespace Symflo\MongoDBODM;

/**
 * Configurator
 * @author Florent Mondoloni
 */
class Configurator
{
    private $config;
    private $defaultConfig = array();

    /**
     * setConfig
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $this->mergeConfig($config);

        if (!class_exists($this->config['baseMongoCollection'])) {
            throw new \InvalidArgumentException(sprintf('Class %s does not exist', $this->config['baseMongoCollection']));
        }
    }

    /**
     * mergeConfig
     * @return array
     */
    private function mergeConfig(array $config)
    {
        $defaultConfig = $this->getDefaultConfig();

        if (array_key_exists('types', $config)) {
            $types = array_merge($defaultConfig['types'], $config['types']);
            unset($defaultConfig['types']);
            $config['types'] = $types;
        }
        
        return array_merge($defaultConfig, $config);
    }

    /**
     * getDefaultConfig
     * @return array
     */
    public function getDefaultConfig()
    {
        if ($this->defaultConfig == array()) {
            $this->defaultConfig = array(
                'user'                => '',
                'password'            => '',
                'host'                => '127.0.0.1',
                'baseMongoCollection' => 'Symflo\MongoDBODM\Document\Collection',
                'types'               => array()
            );    
        }

        return $this->defaultConfig;
    }

    /**
     * Set SetDefaultTypes.
     * @param  $setDefaultTypes SetDefaultTypes value
     */
    public function setDefaultTypes(array $defaultTypes)
    {
        $defaultConfig = $this->getDefaultConfig();
        $defaultConfig['types'] = $defaultTypes;
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * getServer
     * Connection for \MongoClient
     * @return string
     */
    public function getServer()
    {
        return sprintf("mongodb://%s:%s@%s", $this->config['user'], $this->config['password'], $this->config['host']);
    }

    /**
     * getDatabase
     * @return string
     */
    public function getDatabase()
    {
        return $this->config['database'];
    }

    /**
     * getDocuments
     * @return array
     * @throws \InvalidArgumentException If document does not exist in config
     */
    public function getDocuments()
    {
        if (!array_key_exists('documents', $this->config)) {
            throw new \InvalidArgumentException('You must create list documents in your configuration');
        }

        return $this->config['documents'];
    }

    /**
     * supportCollection
     * @param  string $collection
     * @return boolean
     */
    public function supportCollection($collection)
    {
        foreach ($this->getDocuments() as $documentClass) {
            if ($documentClass::COLLECTION_NAME == $collection) {
                return true;
            }
        }

        return false;
    }

    /**
     * getTypeForName
     * @param  string $name
     * @return Symflo\MongoDBODM\Type\TypeInterface
     * @throws \InvalidArgumentException If name does not exist in config
     * @throws \Exception If type is not instance of \Symflo\MongoDBODM\Type\TypeInterface
     */
    public function getTypeForName($name)
    {
        if (!array_key_exists($name, $this->config['types'])) {
            throw new \InvalidArgumentException(sprintf('Type %s is not defined', $name));
        }

        if (!$this->config['types'][$name] instanceof \Symflo\MongoDBODM\Type\TypeInterface) {
            throw new \Exception(sprintf("Type for name %s is not instance of \Symflo\MongoDBODM\Type\TypeInterface", $name));
        }

        return $this->config['types'][$name];
    }

    /**
     * getBaseMongoCollection
     * @return string
     */
    public function getBaseMongoCollection()
    {
        return $this->config['baseMongoCollection'];
    }
}