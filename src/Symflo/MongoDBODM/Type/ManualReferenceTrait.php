<?php

namespace Symflo\MongoDBODM\Type;

/**
 * @author Florent Mondoloni
 */
trait ManualReferenceTrait
{
    protected $reference;

    /**
     * {% inheritdoc %}
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * {% inheritdoc %}
     */
    public function getReference()
    {
        return $this->reference;
    }
}