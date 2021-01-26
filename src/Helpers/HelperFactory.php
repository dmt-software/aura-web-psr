<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Request as AuraRequest;
use Aura\Web\Response as AuraResponse;

use Aura\Web\Request\Url as RequestUrl;

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

    /**
     * Clone a cloned request.
     *
     * @param object $object the object to clone.
     * @param array $overrideProperties the properties to override.
     *
     * @return object
     */
    public static function cloneObject($object, array $overrideProperties = [])
    {
        $helper = new ResponseHelper($object);
        if ($object instanceof AuraRequest) {
            $helper = new RequestHelper($object);
        }

        return $helper->clonedWith($object, $overrideProperties);
    }

    public static function accessProperty($object, $property, $value = null)
    {
        $class = get_class($object);

        switch ($class) {
            case AuraRequest::class:
                $helper = new RequestHelper($object);
                break;
            case AuraResponse::class:
                $helper = new ResponseHelper($object);
                break;
            case AuraRequest\Method::class:
                $helper = new RequestMethodHelper($object);
                break;
        }

        $helper->setObjectProperty($property, $value);
    }

    /**
     * Return a wrapped request url to allow overriding url components.
     *
     * @param RequestUrl $requestUrl
     * @return RequestUrlHelper
     */
    public static function getRequestUrlHelper(RequestUrl $requestUrl): RequestUrlHelper
    {
        return new RequestUrlHelper($requestUrl);
    }
}
