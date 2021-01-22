<?php

namespace DMT\Aura\Psr\Helpers;

use Aura\Web\Request\Url as RequestUrl;

class HelperFactory
{
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
