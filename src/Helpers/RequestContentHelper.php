<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Request\Content as RequestContent;

/**
 * Class RequestContentHelper
 *
 * @package DMT\Aura\Psr\Helpers
 */
class RequestContentHelper extends RequestContent implements PropertyAccessorInterface
{
    use PropertyAccessorTrait {
        PropertyAccessorTrait::__construct as parentConstruct;
    }

    /**
     * RequestContentHelper constructor.
     *
     * @param object|RequestContent $object
     */
    public function __construct(RequestContent $object)
    {
        $object->value = null;
        $object->decoders = ['application/octet-stream' => 'strval'];
        $object->type = 'application/octet-stream';

        $this->parentConstruct($object);
    }
}