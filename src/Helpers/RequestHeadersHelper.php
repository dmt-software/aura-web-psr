<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Request\Headers as RequestHeaders;

/**
 * Class RequestHeadersHelper
 *
 * @package DMT\Aura\Psr\Helpers
 */
class RequestHeadersHelper extends RequestHeaders implements PropertyAccessorInterface
{
    use PropertyAccessorTrait;
}
