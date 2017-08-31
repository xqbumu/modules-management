<?php namespace WebEd\Base\ModulesManagement\Console\Generators\Theme;

class MakeMigration extends \WebEd\Base\ModulesManagement\Console\Generators\Core\MakeMigration
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:make:migration 
        {name : The name of the migration.}
        {--create : The table to be created.}
        {--table= : The table to migrate.}';

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        $module = get_plugin($this->argument('alias'));
        $baseDir = get_base_folder(array_get($module, 'file'));
        $path = $baseDir . 'database/migrations';
        return $path;
    }
}
