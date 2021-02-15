<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Response\Status as ResponseStatus;

/**
 * Class ResponseStatusHelper
 *
 * @package DMT\Aura\Psr\Helpers
 *
 * @deprecated Protocol version fixed in Aura.Web 2.1.1
 */
class ResponseStatusHelper extends ResponseStatus implements PropertyAccessorInterface
{
    use PropertyAccessorTrait;
}