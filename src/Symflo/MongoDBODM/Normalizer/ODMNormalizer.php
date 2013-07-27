<?php

namespace Symflo\MongoDBODM\Normalizer;

use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\RuntimeException;

/**
 * ODMNormalizer
 * @author Florent Mondoloni
 */
class ODMNormalizer extends GetSetMethodNormalizer implements NormalizerInterface, DenormalizerInterface
{
    protected $attributes = array();

    /**
     * Set attributes for normalization
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        $attributes = array();
        foreach ($reflectionMethods as $method) {
            if ($this->isGetMethodAlias($method)) {
                $attributeName = lcfirst(substr($method->name, 3));

                if (!in_array($attributeName, $this->attributes)) {
                    continue;
                }

                if (in_array($attributeName, $this->ignoredAttributes)) {
                    continue;
                }

                $attributeValue = $method->invoke($object);
                if (array_key_exists($attributeName, $this->callbacks)) {
                    $attributeValue = call_user_func($this->callbacks[$attributeName], $attributeValue);
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
