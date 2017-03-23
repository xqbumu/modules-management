<?php

use WebEd\Base\ModulesManagement\Facades\ModulesFacade;

if (!function_exists('webed_plugins_path')) {
    /**
     * @param string $path
     * @return string
     */
    function webed_plugins_path($path = '')
    {
        return base_path('plugins') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('webed_base_path')) {
    /**
     * @param string $path
     * @return string
     */
    function webed_base_path($path = '')
    {
        return base_path('core') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('modules_management')) {
    /**
     * @return \WebEd\Base\ModulesManagement\Support\ModulesManagement
     */
    function modules_management()
    {
        return \WebEd\Base\ModulesManagement\Facades\ModulesManagementFacade::getFacadeRoot();
    }
}

if (!function_exists('get_base_vendor_modules_information')) {
    /**
     * @return array
     */
    function get_base_vendor_modules_information()
    {
        return ModulesFacade::getBaseVendorModules();
    }
}

if (!function_exists('get_all_module_information')) {
    /**
     * @return array
     */
    function get_all_module_information()
    {
        return ModulesFacade::getAllModules();
    }
}

if (!function_exists('get_module_information')) {
    /**
     * @param $alias
     * @return mixed
     */
    function get_module_information($alias)
    {
        return ModulesFacade::findByAlias($alias);
    }
}

if (!function_exists('get_modules_by_type')) {
    /**
     * @param $type
     * @return \Illuminate\Support\Collection
     */
    function get_modules_by_type($type)
    {
        return ModulesFacade::getModulesByType($type);
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
        return ModulesFacade::saveModule($alias, $data);
    }
}
