<?php

namespace KhantNyar\ApiUtils;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KhantNyar\ApiUtils\Skeleton\SkeletonClass
 */
class ApiUtilsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'api-utils';
    }
}
