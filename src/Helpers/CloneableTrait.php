<?php

namespace DMT\Aura\Psr\Helpers;

trait CloneableTrait
{
    /** @var object $object */
    private $object;

    /**
     * CloneableTrait constructor.
     *
     * @param object $object the object to clone.
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Clone an object.
     *
     * @return object
     */
    public function cloneObject()
    {
        $object = clone($this->object);

        $objectProperties = array_filter(get_object_vars($object), 'is_object');
        foreach ($objectProperties as $property => $values) {
            $object->{$property} = clone($values);
        }

        return $object;
    }

    /**
     * Get a cloned version of an object.
     *
     * @param object $object instance of the parent for this class.
     * @param array $overrideProperties list of properties to override.
     *
     * @return object a cloned version of the object with the overridden properties.
     * @throws \RuntimeException on failure to clone the incoming object.
     */
    public function clonedWith($object, array $overrideProperties =[])
    {
        if (!is_a($object, parent::class)) {
            throw new \RuntimeException('unsupported object to clone');
        }

        $object = clone($object);

        $objectProperties = array_filter($overrideProperties + get_object_vars($object), 'is_object');
        foreach ($objectProperties as $property => $values) {
            $object->{$property} = clone($values);
        }

        return $object;
    }
}
