<?php

namespace Symflo\MongoDBODM\Behavior;

/**
 * @author Florent Mondoloni
 */
trait MongoIdTrait
{
    private $_id;
    private $id;

    /**
     * Get Id.
     *
     * @return type Id value
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set Id.
     *
     * @param type $id Id value
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get _id.
     *
     * @return string
     */
    public function get_id()
    {
        return $this->_id;
    }
    
    /**
     * Set _id.
     *
     * @param string $_id
     */
    public function set_id($_id)
    {
        $this->_id = $_id;
        $this->setId($_id);
    }    

    /**
     * getMongoId
     * @return \MongoId
     */
    public function getMongoId()
    {
        if (null === $this->get_id()) {
            return null;
        }
        
        return new \MongoID($this->get_id());
    }
}