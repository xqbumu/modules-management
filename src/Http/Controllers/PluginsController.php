<?php namespace WebEd\Base\ModulesManagement\Http\Controllers;

use WebEd\Base\Http\Controllers\BaseAdminController;
use WebEd\Base\Support\DataTable\DataTables;
use WebEd\Base\ModulesManagement\Http\DataTables\PluginsListDataTable;
use WebEd\Base\ModulesManagement\Repositories\Contracts\PluginsRepositoryContract;
use WebEd\Base\ModulesManagement\Repositories\PluginsRepository;
use Illuminate\Support\Facades\Artisan;
use Yajra\Datatables\Engines\BaseEngine;

class PluginsController extends BaseAdminController
{
    protected $module = 'webed-modules-management';

    protected $dashboardMenuId = 'webed-plugins';

    /**
     * @param PluginsRepository $repository
     */
    public function __construct(PluginsRepositoryContract $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Get index page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(PluginsListDataTable $dataTable)
    {
        $this->breadcrumbs->addLink(trans('webed-modules-management::base.plugins'));

        $this->setPageTitle(trans('webed-modules-management::base.plugins'));

        $this->getDashboardMenu($this->dashboardMenuId);

        $this->dis['dataTable'] = $dataTable->run();

        return do_filter('webed-modules-plugin.index.get', $this)->viewAdmin('plugins-list');
    }

    /**
     * Set data for DataTable plugin
     * @param PluginsListDataTable|BaseEngine $dataTable
     * @return \Illuminate\Http\JsonResponse
     */
    public function postListing(PluginsListDataTable $dataTable)
    {
        return do_filter('datatables.webed-modules-plugin.index.post', $dataTable, $this);
    }

    public function postChangeStatus($module, $status)
    {
        switch ((bool)$status) {
            case true:
                return modules_management()->enableModule($module)->refreshComposerAutoload();
                break;
            default:
                return modules_management()->disableModule($module)->refreshComposerAutoload();
                break;
        }
    }

    public function postInstall($alias)
    {
        $module = get_module_information($alias);

        if(!$module) {
            return response_with_messages(trans('webed-modules-management::base.plugin_not_exists'), true, \Constants::ERROR_CODE);
        }

        Artisan::call('module:install', [
            'alias' => $alias
        ]);

        return response_with_messages(trans('webed-modules-management::base.plugin_installed'));
    }

    public function postUpdate($alias)
    {
        $module = get_module_information($alias);

        if(!$module) {
            return response_with_messages(trans('webed-modules-management::base.plugin_not_exists'), true, \Constants::ERROR_CODE);
        }

        Artisan::call('module:update', [
            'alias' => $alias
        ]);

        return response_with_messages(trans('webed-modules-management::base.plugin_updated'));
    }

    public function postUninstall($alias)
    {
        $module = get_module_information($alias);

        if(!$module) {
            return response_with_messages(trans('webed-modules-management::base.plugin_not_exists'), true, \Constants::ERROR_CODE);
        }

        Artisan::call('module:uninstall', [
            'alias' => $alias
        ]);

        return response_with_messages(trans('webed-modules-management::base.plugin_uninstalled'));
    }
}
