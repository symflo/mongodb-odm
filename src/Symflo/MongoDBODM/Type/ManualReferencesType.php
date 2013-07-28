<?php

namespace Symflo\MongoDBODM\Type;

/**
 * ManualReferencesType
 * @author Florent Mondoloni
 */
class ManualReferencesType implements TypeInterface, ManualReferenceTypeInterface
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
        $refIds = array_unique($value);

        if (count($refIds) == 0) {
            return $refIds;
        }

        $referenceCollectionName = $propertyOptions['reference']::COLLECTION_NAME;
        $references = $this->documentManager
            ->getCollection($referenceCollectionName)
            ->find(array('_id' => array('$in' => $refIds)));

        return $references;
    }
}