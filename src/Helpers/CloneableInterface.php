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
     * Clone an object.
     *
     * @return object
     */
    public function cloneObject();
}