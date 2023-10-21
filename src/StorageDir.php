<?php

namespace Qtran2015\StorageDir;

use Illuminate\Support\Facades\Facade;

class StorageDir extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return StorageDir::class;
    }
}
