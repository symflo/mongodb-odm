<?php

namespace Symflo\MongoDBODM\Type;

/**
 * ManualReferenceType
 * @author Florent Mondoloni
 */
class ManualReferenceType implements TypeInterface, ManualReferenceTypeInterface
{
    use \Symflo\MongoDBODM\Type\ManualReferenceTrait;

    private $documentManager;

    /**
     * __construct
     * @param $documentManager
     */
    public function __construct($documentManager)
    {
        $this->documentManager = $documentManager;
    }
    
    /**
     * {% inheritdoc %}
     */
    public function validate($value)
    {
        return true;
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
    }

    /**
     * {% inheritdoc %}
     */
    public function hydrate($value, $propertyOptions)
    {
        $referenceCollectionName = $propertyOptions['reference']::COLLECTION_NAME;

        $reference = $this->documentManager
            ->getCollection($referenceCollectionName)
            ->findOne(array('_id' => $value));

        return $reference;
    }
}