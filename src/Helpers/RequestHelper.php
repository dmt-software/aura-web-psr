<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Request as AuraRequest;

/**
 * Class Request
 *
 * @package DMT\Aura\Psr\Helpers
 */
class RequestHelper extends AuraRequest implements CloneableInterface, PropertyAccessorInterface
{
    use CloneableTrait;
    use PropertyAccessorTrait {
        CloneableTrait::__construct insteadof PropertyAccessorTrait;
    }
}
