<?php

use Illuminate\Support\Collection;
use WebEd\Base\ModulesManagement\Facades\CoreModulesFacade;

if (!function_exists('webed_core_modules')) {
    /**
     * @return \WebEd\Base\ModulesManagement\Support\CoreModulesSupport
     */
    function webed_core_modules()
    {
        return CoreModulesFacade::getFacadeRoot();
    }
}

if (!function_exists('get_core_module')) {
    /**
     * @param string
     * @return Collection|array
     */
    function get_core_module($alias = null)
    {
        if ($alias) {
            return CoreModulesFacade::findByAlias($alias);
        }
        return CoreModulesFacade::getAllModules();
    }
}

if (!function_exists('get_core_module_version')) {
    /**
     * @param string $alias
     * @return string|null
     */
    function get_core_module_version($alias)
    {
        $module = get_core_module($alias);
        if ($module) {
            return array_get($module, 'version');
        }
        return null;
    }
}

if (!function_exists('save_module_information')) {
    /**
     * @param $alias
     * @param array $data
     * @return bool
     */
    function save_module_information($alias, array $data)
    {
        return CoreModulesFacade::saveModule($alias, $data);
    }
}
