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
}
