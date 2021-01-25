<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Response\Headers as ResponseHeaders;

/**
 * Class ResponseHeadersHelper
 *
 * @package DMT\Aura\Psr\Helpers
 */
class ResponseHeadersHelper extends ResponseHeaders implements PropertyAccessorInterface
{
    use PropertyAccessorTrait;
}
