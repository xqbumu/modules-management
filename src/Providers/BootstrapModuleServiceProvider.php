<?php namespace WebEd\Base\ModulesManagement\Providers;

use Illuminate\Support\ServiceProvider;

class BootstrapModuleServiceProvider extends ServiceProvider
{
    protected $module = 'WebEd\Base\ModulesManagement';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        add_new_template('Homepage', 'Page');

        /**
         * Determine when our app booted
         */
        app()->booted(function () {
            \DashboardMenu::registerItem([
                'id' => 'webed-plugins',
                'piority' => 1001,
                'parent_id' => null,
                'heading' => 'Extensions & themes',
                'title' => 'Plugins',
                'font_icon' => 'icon-paper-plane',
                'link' => route('admin::plugins.index.get'),
                'css_class' => null,
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
