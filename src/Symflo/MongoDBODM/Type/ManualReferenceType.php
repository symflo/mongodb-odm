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
    public function validate($value, $document, $property, $propertyOptions)
    {
        return $this->validateReference(array($value), $propertyOptions);
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
        return 'Reference is not valid';
    }

    /**
     * {% inheritdoc %}
     */
    public function hydrate($value, $propertyOptions)
    {
        $configurator = $this->getConfigurator();
        $referenceClass = $configurator->getClassForDocumentName($propertyOptions['reference']);
        $referenceCollectionName = $configurator->getCollectionNameForDocument($referenceClass);
        
        $reference = $this->documentManager
            ->getCollection($referenceCollectionName)
            ->findOne(array('_id' => $value));

        return $reference;
    }
}