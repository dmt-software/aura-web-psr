<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Request\Url as RequestUrl;

/**
 * Class RequestUrlHelper
 *
 * This helper allows access to the Aura Request Url parts property.
 *
 * @package DMT\Aura\Psr\Helpers
 */
class RequestUrlHelper extends RequestUrl implements PropertyAccessorInterface
{
    use PropertyAccessorTrait;

    /**
     * Access a property.
     *
     * @param string $property the property to access.
     * @param mixed|null $default the default value.
     *
     * @return mixed
     * @throws \InvalidArgumentException when property does not exists
     */
    public function getObjectProperty($property, $default = null)
    {
        if (!property_exists($this->object, $property)) {
            throw new \InvalidArgumentException('property does not exists');
        }

        return $this->object->{$property} ?? $default;
    }
}
