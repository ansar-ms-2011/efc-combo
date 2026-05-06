<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\ApplicationPDFService
 */
class ApplicationPDFService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\ApplicationPDFService::class;
    }
}
