<?php

namespace DMT\Aura\Psr\Helpers;

use InvalidArgumentException;

/**
 * Interface PropertyAccessorInterface
 *
 * Because all properties have protected access in the Aura classes
 * we can use a child class of the original class to access these
 * properties without relying on reflection.
 *
 * @package DMT\Aura\Psr\Helpers
 */
interface PropertyAccessorInterface
{
    /**
     * Access a property.
     *
     * @param string $property the property to access.
     * @param mixed|null $value the value to set.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function setObjectProperty($property, $value = null);
}
