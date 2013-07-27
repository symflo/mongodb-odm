<?php

namespace Symflo\MongoDBODM\Type;

/**
 * ManualReferencesType
 * @author Florent Mondoloni
 */
class ManualReferencesType implements TypeInterface, ManualReferenceTypeInterface
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