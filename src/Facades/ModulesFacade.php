<?php namespace WebEd\Base\ModulesManagement\Facades;

use Illuminate\Support\Facades\Facade;
use WebEd\Base\ModulesManagement\Support\Modules;

/**
 * @method static getAllModules()
 * @method static getBaseVendorModules()
 * @method static getModulesByType(string $type)
 * @method static findByAlias(string $alias)
 * @method static saveModule($alias, array $data)
 */
class ModulesFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Modules::class;
    }
}
