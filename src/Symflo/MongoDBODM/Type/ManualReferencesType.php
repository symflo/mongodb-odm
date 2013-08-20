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
     * Get DocumentManager.
     * @return type DocumentManager value
     */
    public function getDocumentManager()
    {
        return $this->documentManager;
    }

    /**
     * {% inheritdoc %}
     */
    public function validate($value, $document, $property, $propertyOptions)
    {
        return $this->validateReference($value, $propertyOptions);
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
        'References are not valid';
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

        $configurator = $this->getDocumentManager()->getConnection()->getConfigurator();
        $referenceClass = $configurator->getClassForDocumentName($propertyOptions['reference']);
        $referenceCollectionName = $configurator->getCollectionNameForDocument($referenceClass);
        
        $references = $this->getDocumentManager()
            ->getCollection($referenceCollectionName)
            ->find(array('_id' => array('$in' => $refIds)));

        return $references;
    }
}