<?php use Illuminate\Routing\Router;

/**
 *
 * @var Router $router
 *
 */

$router->group(['middleware' => 'web'], function (Router $router) {

    $adminRoute = config('webed.admin_route');

    $moduleRoute = 'modules-management';

    /**
     * Admin routes
     */
    $router->group(['prefix' => $adminRoute . '/' . $moduleRoute, 'middleware' => 'has-role:super-admin'], function (Router $router) use ($adminRoute, $moduleRoute) {
        $router->get('', function () {
            return redirect(route('admin::core-modules.index.get'));
        });
        $router->get('plugins', 'PluginsController@getIndex')
            ->name('admin::plugins.index.get');

        $router->post('plugins', 'PluginsController@postListing')
            ->name('admin::plugins.index.post');

        $router->post('plugins/change-status/{module}/{status}', 'PluginsController@postChangeStatus')
            ->name('admin::plugins.change-status.post');

        $router->post('plugins/install/{module}', 'PluginsController@postInstall')
            ->name('admin::plugins.install.post');

        $router->post('plugins/uninstall/{module}', 'PluginsController@postUninstall')
            ->name('admin::plugins.uninstall.post');
    });
});
