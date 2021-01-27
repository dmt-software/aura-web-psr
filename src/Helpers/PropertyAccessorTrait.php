<?php

namespace DMT\Aura\Psr\Helpers;

/**
 * Trait PropertyAccessorTrait
 *
 * @package DMT\Aura\Psr\Helpers
 */
trait PropertyAccessorTrait
{
    /** @var object $object */
    private $object;

    /**
     * PropertyAccessorTrait constructor.
     *
     * @param object $object the object ot gain access to.
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Access a property.
     *
     * @param string $property the property to access.
     * @param mixed|null $value the value to set.
     *
     * @return void
     * @throws \InvalidArgumentException when property does not exists
     */
    public function setObjectProperty($property, $value = null)
    {
        if (!property_exists($this->object, $property)) {
            throw new \InvalidArgumentException('property does not exists');
        }

        $this->object->{$property} = $value;
    }
}