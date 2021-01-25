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
     * Set a value inside request url.
     *
     * @param int|array $component
     * @param string|null $value
     */
    public function set($component, string $value = null)
    {
        if (is_array($component)) {
            $this->object->parts = $component;
            $this->schemeIsSecure($this->object->parts['scheme'] ?? '');
            return;
        }

        $key = $this->keys[$component] ?? $component;
        if ($key !== null) {
            $this->object->parts[$key] = $value;
        }

        if ($key === 'scheme') {
            $this->schemeIsSecure($value);
        }
    }

    /**
     * Set the url as secure when scheme is set to https.
     *
     * @param string $scheme
     */
    protected function schemeIsSecure(string $scheme): void
    {
        $this->object->secure = stripos($scheme, 'https') === 0;
    }
}
