<?php namespace WebEd\Base\ModulesManagement\Hook;


class ShowCmsUpdateNotification
{
    protected $modules;

    public function __construct()
    {
        $this->modules = get_core_module();
    }

    public function handle()
    {
        $needToUpdate = 0;
        foreach ($this->modules as $module) {
            if (
                get_core_module_composer_version(array_get($module, 'repos')) === array_get($module, 'installed_version')
                || !module_version_compare(get_core_module_composer_version(array_get($module, 'repos')), '^' . array_get($module, 'installed_version', 0))
            ) {
                continue;
            }
            $needToUpdate++;
        }
        if ($needToUpdate) {
            echo html()->note(view('webed-modules-management::admin.update-cms.message', ['modulesCount' => $needToUpdate]), 'warning', false);
        }
    }
}
