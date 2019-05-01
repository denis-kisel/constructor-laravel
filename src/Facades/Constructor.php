<?php

namespace DenisKisel\LaravelAdminReadySolution\Facades;

use Illuminate\Support\Facades\Facade;

class Constructor extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'constructor';
    }
}
