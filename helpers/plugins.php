<?php

use Illuminate\Support\Collection;
use WebEd\Base\ModulesManagement\Facades\PluginsFacade;

if (!function_exists('webed_plugins')) {
    /**
     * @return \WebEd\Base\ModulesManagement\Support\PluginsSupport
     */
    function webed_plugins()
    {
        return PluginsFacade::getFacadeRoot();
    }
}

if (!function_exists('get_plugin')) {
    /**
     * @param string
     * @return Collection
     */
    function get_plugin($alias = null)
    {
        if ($alias) {
            return PluginsFacade::findByAlias($alias);
        }
        return PluginsFacade::getAllPlugins();
    }
}

if (!function_exists('save_plugin_information')) {
    /**
     * @param $alias
     * @param array $data
     * @return bool
     */
    function save_plugin_information($alias, array $data)
    {
        return PluginsFacade::savePlugin($alias, $data);
    }
}
