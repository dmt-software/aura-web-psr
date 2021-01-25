<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Request\Method as RequestMethod;

/**
 * Class RequestMethodHelper
 *
 * @package DMT\Aura\Psr\Helpers
 */
class RequestMethodHelper extends RequestMethod implements PropertyAccessorInterface
{
    use PropertyAccessorTrait;
}