<?php use Illuminate\Routing\Router;

/**
 *
 * @var Router $router
 *
 */
/**
 * Admin routes
 */
$adminRoute = config('webed.admin_route');

$moduleRoute = 'DummyAlias';

$router->group(['prefix' => $adminRoute . '/' . $moduleRoute], function (Router $router) use ($adminRoute, $moduleRoute) {
    /**
     *
     * Put some route here
     *
     */
});
