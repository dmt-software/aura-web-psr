<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Request as AuraRequest;
use Aura\Web\Response as AuraResponse;

/**
 * Class HelperFactory
 *
 * These helpers help to access object properties within Aura Web that are normally
 * inaccessible without depending on reflection.
 * Without this access Aura Web can not act as a psr-7 implementation.
 *
 * @package DMT\Aura\Psr\Helpers
 */
class HelperFactory
{
    /**
     * Create a helper for an object
     *
     * @param object $object the object for which a helper is requested
     * @return object the object helper
     * @throws \InvalidArgumentException for an unsupported object
     */
    public function createHelper($object)
    {
        $class = get_class($object);

        switch ($class) {
            case AuraRequest::class:
                return new RequestHelper($object);
            case AuraRequest\Content::class:
                return new RequestContentHelper($object);
            case AuraRequest\Headers::class:
                return new RequestHeadersHelper($object);
            case AuraRequest\Method::class:
                return new RequestMethodHelper($object);
            case AuraRequest\Url::class:
                return new RequestUrlHelper($object);
            case AuraResponse::class:
                return new ResponseHelper($object);
            case AuraResponse\Headers::class:
                return new ResponseHeadersHelper($object);
            case AuraResponse\Status::class:
                return new ResponseStatusHelper($object);
            default:
                throw new \InvalidArgumentException('unsupported object');
        }
    }
}
