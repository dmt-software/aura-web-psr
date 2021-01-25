<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Response\Status as ResponseStatus;

/**
 * Class ResponseStatusHelper
 *
 * @package DMT\Aura\Psr\Helpers
 */
class ResponseStatusHelper extends ResponseStatus implements PropertyAccessorInterface
{
    use PropertyAccessorTrait;
}