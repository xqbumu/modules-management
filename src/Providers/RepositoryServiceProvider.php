<?php namespace WebEd\Base\ModulesManagement\Providers;

use Illuminate\Support\ServiceProvider;
use WebEd\Base\ModulesManagement\Models\CoreModules;
use WebEd\Base\ModulesManagement\Models\Plugins;
use WebEd\Base\ModulesManagement\Repositories\Contracts\CoreModulesRepositoryContract;
use WebEd\Base\ModulesManagement\Repositories\Contracts\PluginsRepositoryContract;
use WebEd\Base\ModulesManagement\Repositories\CoreModulesRepository;
use WebEd\Base\ModulesManagement\Repositories\CoreModulesRepositoryCacheDecorator;
use WebEd\Base\ModulesManagement\Repositories\PluginsRepository;
use WebEd\Base\ModulesManagement\Repositories\PluginsRepositoryCacheDecorator;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PluginsRepositoryContract::class, function () {
            $repository = new PluginsRepository(new Plugins());

            if (config('webed-caching.repository.enabled')) {
                return new PluginsRepositoryCacheDecorator($repository);
            }

            return $repository;
        });

        $this->app->bind(CoreModulesRepositoryContract::class, function () {
            $repository = new CoreModulesRepository(new CoreModules());

            if (config('webed-caching.repository.enabled')) {
                return new CoreModulesRepositoryCacheDecorator($repository);
            }

            return $repository;
        });
    }
}
