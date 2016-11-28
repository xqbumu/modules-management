<?php namespace WebEd\Base\ModulesManagement\Providers;

use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    protected $module = 'WebEd\Base\ModulesManagement';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->generatorCommands();
        $this->otherCommands();
    }

    private function generatorCommands()
    {
        $this->commands([
            \WebEd\Base\ModulesManagement\Console\Generators\MakeModule::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeProvider::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeController::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeMiddleware::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeRequest::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeModel::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeRepository::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeFacade::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeService::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeSupport::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeView::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeMigration::class,
            \WebEd\Base\ModulesManagement\Console\Generators\MakeCommand::class,
        ]);
    }

    private function otherCommands()
    {
        $this->commands([
            \WebEd\Base\ModulesManagement\Console\Commands\InstallCmsCommand::class,
            \WebEd\Base\ModulesManagement\Console\Commands\InstallModuleCommand::class,
            \WebEd\Base\ModulesManagement\Console\Commands\UninstallModuleCommand::class,
            \WebEd\Base\ModulesManagement\Console\Commands\DisableModuleCommand::class,
            \WebEd\Base\ModulesManagement\Console\Commands\EnableModuleCommand::class,
            \WebEd\Base\ModulesManagement\Console\Commands\ExportModuleCommand::class,
            \WebEd\Base\ModulesManagement\Console\Commands\GetAllModulesCommand::class,
        ]);
    }
}
