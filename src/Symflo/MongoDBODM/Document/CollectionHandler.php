<?php

namespace Symflo\MongoDBODM\Document;

use Symflo\MongoDBODM\Normalizer\ODMNormalizer;
use Symflo\MongoDBODM\Document\DocumentInterface;

/**
 * CollectionHandler
 * @author Florent Mondoloni
 */
class CollectionHandler
{
    protected $joins = array();
    private $documentManager;

    /**
     * __construct
     * @param ODMNormalizer $normalizer
     */
    public function __construct(ODMNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * setCollection
     * @param $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * Get Normalizer.
     * @return NormalizerInterface
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }

    /**
     * Set DocumentManager.
     * @param  $documentManager DocumentManager value
     */
    public function setDocumentManager($documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * addJoin
     * Property to join
     * @param string $join
     * @return Collection
     */
    public function addJoin($join)
    {
        $this->joins[] = $join;
        return $this;
    }

    /**
     * joins
     * array properties to join
     * @param  array  $joins
     * @return Collection
     */
    public function joins(array $joins)
    {
        $this->joins = $joins;
        return $this;
    }

    /**
     * hasJoins
     * @return boolean
     */
    public function hasJoins()
    {
        return (bool) count($this->joins);
    }

    /**
     * hasJoins
     * @return boolean
     */
    public function hasJoin($join)
    {
        return (bool) in_array($join, $this->joins);
    }

    /**
     * getId
     * @param  DocumentInterface|array $document
     * @return \MongoId
     */
    public static function getId($document)
    {
        if ($document instanceOf DocumentInterface) {
            $mongoId = $document->getMongoId();    
        } elseif (array_key_exists('_id', $document)) {
            $mongoId = $document['_id'];
        } else {
            throw new \Exception("Document must implement Symflo\MongoDBODM\Document\DocumentInterface or $key _id");
        }

        return $mongoId;
    }

    /**
     * getCollectionIds
     * @param  array|collection DocumentInterface $collection
     * @return array
     */
    public static function getCollectionIds($collection)
    {
        $mongoIds = array();
        foreach ($collection as $document) {
            $mongoIds[] = self::getId($document);
        }

        return $mongoIds;
    }

    /**
     * getReferenceManualForDocument
     * @param  DocumentInterface $document
     * @param  string $type
     * @return array property with reference Manual
     */
    public static function getPropertiesForTypeForDocument(DocumentInterface $document, $type)
    {
        $referenceManualProperties = array();
        foreach ($document->getProperties() as $property => $typeOptions) {
            if ($typeOptions['type'] == $type) {
                $referenceManualProperties[] = array(
                    'property'  => $property, 
                    'reference' => $typeOptions['reference'],
                    'target'    => (array_key_exists('target', $typeOptions)) ? $typeOptions['target']: $property
                    );
            }
        }

        return $referenceManualProperties;
    }

    /**
     * __call
     */
    public function __call($name, $args)
    {
        $data = call_user_func_array(array($this->collection, $name), $args);

        if ($data instanceOf \MongoCursor) {
            $documentCollection = new DocumentCollection();
            foreach ($data as $array) {
                $document = $this->hydrateDocument($array);
                $documentCollection->add($document);
            }

            $this->joins(array());
            return $documentCollection;
        } elseif (is_array($data)) {
            if (array_key_exists('_id', $data)) {
                return $this->hydrateDocument($data);
            }
        }

        return $data;
    }


    private function hydrateDocument(array $array)
    {
        $document = $this->normalizer->denormalize($array, $this->collection->getDocumentClass());
        if (!$this->hasJoins()) {
            return $document;
        }

        foreach (self::getPropertiesForTypeForDocument($document, 'manualReference') as $propertyOptions) {
            if (!$this->hasJoin($propertyOptions['property'])) {
                continue;
            }

            $getter = 'get'.ucfirst($propertyOptions['property']);
            $setter = 'set'.ucfirst($propertyOptions['target']);
            $refId = $document->$getter();

            $referenceCollectionName = $propertyOptions['reference']::COLLECTION_NAME;

            $reference = $this->documentManager
                ->getCollection($referenceCollectionName)
                ->findOne(array('_id' => $refId));

            $document->$setter($reference);
        }

        foreach (self::getPropertiesForTypeForDocument($document, 'manualReferences') as $propertyOptions) {
            if (!$this->hasJoin($propertyOptions['property'])) {
                continue;
            }

            $getter = 'get'.ucfirst($propertyOptions['property']);
            $setter = 'set'.ucfirst($propertyOptions['target']);
            $refIds = array_unique($document->$getter());

            if (count($refIds) == 0) {
                continue;
            }

            $referenceCollectionName = $propertyOptions['reference']::COLLECTION_NAME;
            $references = $this->documentManager
                ->getCollection($referenceCollectionName)
                ->find(array('_id' => array('$in' => $refIds)));
            $document->$setter($references);
        }

        return $document;
    }
}