<?php namespace WebEd\Base\ModulesManagement\Console\Commands;

use Illuminate\Console\Command;

class UpdateCoreModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:update {alias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update WebEd core';

    /**
     * @var array
     */
    protected $container = [];

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $app;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->app = app();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = get_core_module($this->argument('alias'));

        if (!$module) {
            $this->error('Module not exists');
            die();
        }

        if (get_core_module_composer_version(array_get($module, 'repos')) === array_get($module, 'installed_version')) {
            $this->info("\nModule " . $this->argument('alias') . " already up to date.");
            return;
        }

        $this->registerUpdateModuleService($module);

        $this->info("\nModule " . $this->argument('alias') . " has been updated.");
    }

    protected function registerUpdateModuleService($module)
    {
        $this->line('Update module dependencies...');

        $namespace = str_replace('\\\\', '\\', array_get($module, 'namespace', '') . '\Providers\UpdateModuleServiceProvider');
        if (class_exists($namespace)) {
            $this->app->register($namespace);
        }

        webed_core_modules()->saveModule($module, [
            'installed_version' => isset($module['version']) ? $module['version'] : get_core_module_composer_version(array_get($module, 'repos')),
        ]);

        $moduleProvider = str_replace('\\\\', '\\', array_get($module, 'namespace', '') . '\Providers\ModuleProvider');
        \Artisan::call('vendor:publish', [
            '--provider' => $moduleProvider,
            '--tag' => 'webed-public-assets',
            '--force' => true
        ]);

        \Artisan::call('cache:clear');

        $this->line('Your module has been updated');
    }
}
