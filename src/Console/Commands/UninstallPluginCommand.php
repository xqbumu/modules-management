<?php namespace WebEd\Base\ModulesManagement\Console\Commands;

use Illuminate\Console\Command;

class UninstallPluginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugin:uninstall {alias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall WebEd plugin';

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
        /**
         * Migrate tables
         */
        $this->line('Uninstall plugin dependencies...');
        $this->registerUninstallModuleService();

        $this->info("\nPlugin " . $this->argument('alias') . " uninstalled.");
    }

    protected function registerUninstallModuleService()
    {
        $module = get_plugin($this->argument('alias'));
        $namespace = str_replace('\\\\', '\\', array_get($module, 'namespace', '') . '\Providers\UninstallModuleServiceProvider');
        if(class_exists($namespace)) {
            $this->app->register($namespace);
        }
        webed_plugins()->savePlugin($module, [
            'installed' => false,
            'installed_version' => '',
        ]);
        \Artisan::call('cache:clear');
        $this->line('Uninstalled');
    }
}
