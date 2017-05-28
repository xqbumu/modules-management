<?php namespace WebEd\Base\ModulesManagement\Console\Commands;

use Illuminate\Console\Command;

class UpdatePluginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugin:update {alias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update WebEd plugin';

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
        $module = get_plugin($this->argument('alias'));

        if (!$module) {
            $this->error('Plugin not exists');
            die();
        }

        if (array_get($module, 'version') === array_get($module, 'installed_version')) {
            $this->info("\nPlugin " . $this->argument('alias') . " already up to date.");
            return;
        }

        $this->registerUpdateModuleService($module);

        $this->info("\nPlugin " . $this->argument('alias') . " has been updated.");
    }

    protected function registerUpdateModuleService($module)
    {
        $this->line('Update plugin dependencies...');

        $namespace = str_replace('\\\\', '\\', array_get($module, 'namespace', '') . '\Providers\UpdateModuleServiceProvider');
        if (class_exists($namespace)) {
            $this->app->register($namespace);
        }

        webed_plugins()->savePlugin($module, [
            'installed_version' => array_get($module, 'version'),
        ]);

        $moduleProvider = str_replace('\\\\', '\\', array_get($module, 'namespace', '') . '\Providers\ModuleProvider');
        \Artisan::call('vendor:publish', [
            '--provider' => $moduleProvider,
            '--tag' => 'webed-public-assets',
            '--force' => true
        ]);

        \Artisan::call('cache:clear');

        $this->line('Your plugin has been updated');
    }
}
