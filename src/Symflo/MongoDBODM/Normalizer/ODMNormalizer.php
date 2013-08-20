<?php

namespace Symflo\MongoDBODM\Normalizer;

use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symflo\MongoDBODM\Document\DocumentCollection;
use Symflo\MongoDBODM\Document\DocumentInterface;

/**
 * ODMNormalizer
 * @author Florent Mondoloni
 */
class ODMNormalizer extends GetSetMethodNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $attributesChoice = array_keys($object->getProperties());
        if (in_array('id', $attributesChoice)) {
            unset($attributesChoice[array_search('id', $attributesChoice)]);
        }

        if (method_exists($object, 'get_id') && null !== $object->get_id()) {
            $attributesChoice[] = '_id';
        }

        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        $attributes = array();
        foreach ($reflectionMethods as $method) {
            if ($this->isGetMethodAlias($method)) {
                $attributeName = lcfirst(substr($method->name, 3));

                if (!in_array($attributeName, $attributesChoice)) {
                    continue;
                }

                if (in_array($attributeName, $this->ignoredAttributes)) {
                    continue;
                }
                
                $attributeValue = $method->invoke($object);

                if (array_key_exists($attributeName, $this->callbacks)) {
                    $attributeValue = call_user_func($this->callbacks[$attributeName], $attributeValue);
                }

                if (null !== $attributeValue) {
                    if ($attributeValue instanceof DocumentInterface) {
                        $attributeValue = $this->normalize($attributeValue, $format);
                    }

                    if ($attributeValue instanceof DocumentCollection) {
                        $newAttributeValue = array();
                        foreach ($attributeValue as $value) {
                            $newAttributeValue[] = $this->normalize($value, $format);
                        }

                        $attributeValue = $newAttributeValue;
                    }
                }

                $attributes[$attributeName] = $attributeValue;
            }
        }

        return $attributes;
    }

    protected function isGetMethodAlias(\ReflectionMethod $method)
    {
        return (
            0 === strpos($method->name, 'get') &&
            3 < strlen($method->name) &&
            0 === $method->getNumberOfRequiredParameters()
        );
    }
}
