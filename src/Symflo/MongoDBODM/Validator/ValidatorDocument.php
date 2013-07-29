<?php

namespace Symflo\MongoDBODM\Validator;

use Symflo\MongoDBODM\Document\DocumentInterface;
use Symflo\MongoDBODM\Configurator;

/**
 * ValidatorDocument
 * @author Florent Mondoloni
 */
class ValidatorDocument implements ValidatorDocumentInterface
{
    protected $errors = array();

    /**
     * __construct
     * @param Configurator $configurator
     */
    public function __construct(Configurator $configurator)
    {
        $this->configurator = $configurator;
    }

    /**
     * {% inheritdoc %}
     */
    public function validate(DocumentInterface $document)
    {
        foreach ($document->getProperties() as $property => $typeOptions) {
            $typeOptions = array_merge($this->getDefaultOptionsType(), $typeOptions);
            $method = 'get'.ucfirst($property);
            $value = $document->$method();
            $type = $this->configurator->getTypeForName($typeOptions['type']);
            if (empty($value) && false === $typeOptions['required']) {
                continue;
            } elseif ((empty($value) && $typeOptions['required'])) {
                $this->addError(array(
                    'document' => get_class($document),
                    'property' => $property,
                    'message'  => 'field is required'
                ));
            }

            if (false === $type->validate($value)) {
                $this->addError(array(
                    'document' => get_class($document),
                    'property' => $property,
                    'message'  => $type->getError()
                ));
            }
        }

        return (bool) !(count($this->getErrors()) > 0);
    }

    /**
     * getDefaultOptionsType
     * @return array
     */
    public function getDefaultOptionsType()
    {
        return array(
            'type'     => 'pass',
            'required' => false
        );
    }

    /**
     * {% inheritdoc %}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    protected function addError($error)
    {
        $this->errors[] = $error;
    }
}