<?php

namespace Symflo\MongoDBODM\Behavior;

/**
 * @author Florent Mondoloni
 */
trait MongoIdTrait
{
    private $_id;

    /**
     * Get _id.
     *
     * @return string
     */
    public function get_Id()
    {
        return $this->_id;
    }
    
    /**
     * Set _id.
     *
     * @param string $_id
     */
    public function set_Id($_id)
    {
        $this->_id = $_id;
    }    

    /**
     * getMongoId
     * @return \MongoId
     */
    public function getMongoId()
    {
        if (null === $this->get_Id()) {
            return null;
        }
        
        return new \MongoID($this->get_Id());
    }
}