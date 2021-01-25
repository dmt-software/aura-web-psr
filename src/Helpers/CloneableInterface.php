<?php

namespace DMT\Aura\Psr\Helpers;

/**
 * Interface CloneableInterface
 *
 * Because all properties have protected access in the Aura classes
 * we can use a child class of the original class to access these
 * properties without relying on reflection.
 *
 * @package DMT\Aura\Psr\Helpers
 */
interface CloneableInterface
{
    /**
     * Get a cloned version of an object.
     *
     * @param object $object instance of the parent for this class.
     * @param array $overrideProperties list of properties to override.
     *
     * @return object a cloned version of the object with the overridden properties.
     * @throws \RuntimeException on failure to clone the incoming object.
     */
    public function clonedWith($object, array $overrideProperties =[]);
}