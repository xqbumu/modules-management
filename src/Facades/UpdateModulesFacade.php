<?php namespace WebEd\Base\ModulesManagement\Facades;

use Illuminate\Support\Facades\Facade;
use WebEd\Base\ModulesManagement\Support\UpdateModulesSupport;

class UpdateModulesFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return UpdateModulesSupport::class;
    }
}
