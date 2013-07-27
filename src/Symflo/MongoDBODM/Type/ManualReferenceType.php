<?php

namespace Symflo\MongoDBODM\Type;

/**
 * ManualReferenceType
 * @author Florent Mondoloni
 */
class ManualReferenceType implements TypeInterface, ManualReferenceTypeInterface
{
    use \Symflo\MongoDBODM\Type\ManualReferenceTrait;
    
    /**
     * {% inheritdoc %}
     */
    public function validate($value)
    {
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
    }
}