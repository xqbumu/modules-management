<?php namespace WebEd\Base\ModulesManagement\Http\Controllers;

use WebEd\Base\Http\Controllers\BaseAdminController;
use WebEd\Base\ModulesManagement\Http\DataTables\CoreModulesListDataTable;
use Illuminate\Support\Facades\Artisan;
use Yajra\Datatables\Engines\BaseEngine;

class CoreModulesController extends BaseAdminController
{
    protected $module = 'webed-modules-management';

    protected $dashboardMenuId = 'webed-core-modules';

    /**
     * Get index page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(CoreModulesListDataTable $dataTable)
    {
        $this->breadcrumbs->addLink(trans($this->module . '::base.plugins'));

        $this->setPageTitle(trans($this->module . '::base.core_modules'));

        $this->getDashboardMenu($this->dashboardMenuId);

        $this->dis['dataTable'] = $dataTable->run();

        return do_filter('webed-modules-plugin.index.get', $this)->viewAdmin('core-modules-list');
    }

    /**
     * Set data for DataTable plugin
     * @param CoreModulesListDataTable|BaseEngine $dataTable
     * @return \Illuminate\Http\JsonResponse
     */
    public function postListing(CoreModulesListDataTable $dataTable)
    {
        return do_filter('datatables.webed-modules-plugin.index.post', $dataTable, $this);
    }

    public function postUpdate($alias)
    {
        $module = get_core_module($alias);

        if(!$module) {
            return response_with_messages(trans($this->module . '::base.core_module_not_exists'), true, \Constants::ERROR_CODE);
        }

        Artisan::call('core:update', [
            'alias' => $alias
        ]);

        return response_with_messages(trans($this->module . '::base.core_module_updated'));
    }
}
