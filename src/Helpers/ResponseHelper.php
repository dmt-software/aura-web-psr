<?php


namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Response as AuraResponse;

/**
 * Class Response
 *
 * @package DMT\Aura\Psr\Helpers
 */
class ResponseHelper extends AuraResponse implements CloneableInterface
{
    use CloneableTrait;
    use PropertyAccessorTrait {
        CloneableTrait::__construct insteadof PropertyAccessorTrait;
    }
}