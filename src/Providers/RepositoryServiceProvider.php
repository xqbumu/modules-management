<?php namespace WebEd\Base\ModulesManagement\Providers;

use Illuminate\Support\ServiceProvider;
use WebEd\Base\ModulesManagement\Models\Plugins;
use WebEd\Base\ModulesManagement\Repositories\Contracts\PluginsRepositoryContract;
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
    }
}
