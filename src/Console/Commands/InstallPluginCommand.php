<?php namespace WebEd\Base\ModulesManagement\Console\Commands;

use Illuminate\Console\Command;

class InstallPluginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugin:install {alias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install WebEd plugin';

    /**
     * @var array
     */
    protected $container = [];

    /**
     * @var array
     */
    protected $dbInfo = [];

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
            $this->error('Module not exists');
            die();
        }

        if (array_get($module, 'installed') === true) {
            $this->info("\nModule " . $this->argument('alias') . " installed.");
            return;
        }

        $this->detectRequiredDependencies($module);

        $this->registerInstallModuleService($module);

        $this->info("\nModule " . $this->argument('alias') . " installed.");
    }

    protected function registerInstallModuleService($module)
    {
        /**
         * Migrate tables
         */
        $this->line('Migrate database...');
        \Artisan::call('migrate');
        $this->line('Install module dependencies...');

        $namespace = str_replace('\\\\', '\\', array_get($module, 'namespace', '') . '\Providers\InstallModuleServiceProvider');
        if (class_exists($namespace)) {
            $this->app->register($namespace);

        }
        /**
         * Publish assets
         */
        $moduleProvider = str_replace('\\\\', '\\', array_get($module, 'namespace', '') . '\Providers\ModuleProvider');
        \Artisan::call('vendor:publish', [
            '--provider' => $moduleProvider,
            '--tag' => 'webed-public-assets',
            '--force' => true
        ]);
        webed_plugins()->savePlugin($module, [
            'installed' => true,
            'installed_version' => array_get($module, 'version'),
        ]);
        \Artisan::call('cache:clear');
        $this->line('Installed');
    }

    protected function detectRequiredDependencies($module)
    {
        $checkRelatedModules = check_module_require($module);
        if ($checkRelatedModules['error']) {
            foreach ($checkRelatedModules['messages'] as $message) {
                $this->error($message);
            }
            die();
        }
    }
}
