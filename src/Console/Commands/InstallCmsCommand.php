<?php namespace WebEd\Base\ModulesManagement\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;
use Illuminate\Filesystem\Filesystem;
use WebEd\Base\ACL\Models\EloquentRole;
use WebEd\Base\Users\Models\EloquentUser;

class InstallCmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:install {--refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install WebEd';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var array
     */
    protected $container = [];

    /**
     * @var array
     */
    protected $dbInfo = [];

    /**
     * @var EloquentRole
     */
    protected $role;

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $app;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->files = $filesystem;

        $this->app = app();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getDatabaseInformation();
        /**
         * Migrate tables
         */
        $this->line('Migrate database...');
        if($this->option('refresh')) {
            \Artisan::call('migrate:refresh');
        } else {
            \Artisan::call('migrate');
        }

        $this->line('Create super admin role...');
        $this->createSuperAdminRole();
        $this->line('Create admin user...');
        $this->createAdminUser();
        $this->line('Install module dependencies...');
        $this->registerInstallModuleService();

        session()->flush();
        session()->regenerate();
        \Artisan::call('cache:clear');

        $this->info("\nWebEd installed. Current version is " . config('webed.version'));
    }

    /**
     * Get database information
     */
    protected function getDatabaseInformation()
    {
        $this->dbInfo['connection'] = env('DB_CONNECTION');
        $this->dbInfo['host'] = env('DB_HOST');
        $this->dbInfo['database'] = env('DB_DATABASE');
        $this->dbInfo['username'] = env('DB_USERNAME');
        $this->dbInfo['password'] = env('DB_PASSWORD');
        $this->dbInfo['port'] = env('DB_PORT');

        while (!check_db_connection()) {

            $this->info('Setup your database...');

            $this->dbInfo['connection'] = $this->ask('Your database type', 'mysql');
            $this->dbInfo['host'] = $this->ask('Your database host', 'localhost');
            $this->dbInfo['database'] = $this->ask('Your database name', 'webed');
            $this->dbInfo['username'] = $this->ask('Your database username', 'root');

            $question = new Question('Your database password', false);
            $question->setHidden(true)->setHiddenFallback(true);
            $this->dbInfo['password'] = (new SymfonyQuestionHelper())->ask($this->input, $this->output, $question);
            if ($this->dbInfo['password'] === false) {
                $this->dbInfo['password'] = '';
            }
            $this->line('');

            $this->dbInfo['port'] = $this->ask('Your database port', '3306');
            $this->line('');

            $this->info('Saving database information to .env...');

            $contents = $this->getEnvFile();
            $contents = preg_replace('/(' . preg_quote('DB_CONNECTION=') . ')(.*)/', 'DB_CONNECTION=' . $this->dbInfo['connection'], $contents);
            $contents = preg_replace('/(' . preg_quote('DB_HOST=') . ')(.*)/', 'DB_HOST=' . $this->dbInfo['host'], $contents);
            $contents = preg_replace('/(' . preg_quote('DB_DATABASE=') . ')(.*)/', 'DB_DATABASE=' . $this->dbInfo['database'], $contents);
            $contents = preg_replace('/(' . preg_quote('DB_USERNAME=') . ')(.*)/', 'DB_USERNAME=' . $this->dbInfo['username'], $contents);
            $contents = preg_replace('/(' . preg_quote('DB_PASSWORD=') . ')(.*)/', 'DB_PASSWORD=' . $this->dbInfo['password'], $contents);
            $contents = preg_replace('/(' . preg_quote('DB_PORT=') . ')(.*)/', 'DB_PORT=' . $this->dbInfo['port'], $contents);

            // Write to .env
            $this->files->put('.env', $contents);

            if (!check_db_connection()) {
                $this->error('Can not connect to database, please try again!');
            } else {
                $this->info('Connect to database successfully!');
            }
        }

        $this->info('Database OK...');
    }

    protected function createSuperAdminRole()
    {
        $role = EloquentRole::where('slug', '=', 'super-admin')->first();
        if($role) {
            $this->role = $role;
            $this->info('Role already exists...');
            return;
        }

        $role = new EloquentRole();
        $role->name = 'Super Admin';
        $role->slug = 'super-admin';

        try {
            $role->save();
            $this->info('Role created successfully...');
            $this->role = $role;
        } catch (\Exception $exception) {
            $this->error('Error occurred when create role...');
        }
    }

    protected function createAdminUser()
    {
        $user = new EloquentUser();
        $user->username = $this->ask('Your username', 'admin');
        $user->email = $this->ask('Your email', 'admin@webed.com');
        $user->password = $this->secret('Your password');
        $user->display_name = $this->ask('Your display name', 'Super Admin');
        $user->first_name = $this->ask('Your first name', 'Admin');
        $user->last_name = $this->ask('Your last_name', false);

        try {
            $user->save();
            $this->info('User created successfully...');
        } catch (\Exception $exception) {
            $this->error('Error occurred when create user...');
        }

        /**
         * Assign this user to super admin
         */
        if($this->role) {
            $this->role->users()->save($user);
        }
    }

    protected function registerInstallModuleService()
    {
        $modules = get_modules_by_type('base');
        foreach ($modules as $module) {
            $namespace = str_replace('\\\\', '\\', array_get($module, 'namespace', '') . '\Providers\InstallModuleServiceProvider');
            if(class_exists($namespace)) {
                $this->app->register($namespace);
            }
        }
        \Artisan::call('vendor:publish', [
            '--tag' => 'webed-public-assets',
        ]);
    }

    /**
     * @return string
     */
    protected function getEnvFile()
    {
        return $this->files->exists('.env') ? $this->files->get('.env') : $this->files->get('.env.example') ?: '';
    }
}
