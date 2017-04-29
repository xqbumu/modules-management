<?php namespace WebEd\Base\ModulesManagement\Support;

use Illuminate\Support\Collection;
use WebEd\Base\ModulesManagement\Repositories\Contracts\PluginsRepositoryContract;
use WebEd\Base\ModulesManagement\Repositories\PluginsRepository;

class Modules
{
    /**
     * @var array
     */
    protected $modules;

    /**
     * @var Collection
     */
    protected $modulesCollection;

    /**
     * @var PluginsRepository
     */
    protected $pluginRepository;

    public function __construct(PluginsRepositoryContract $pluginsRepository)
    {
        $this->pluginRepository = $pluginsRepository;
    }

    /**
     * @return array
     */
    public function getAllModules()
    {
        if ($this->modules) {
            return $this->modules;
        }

        $modulesArr = [];

        $canAccessDB = true;
        if (app()->runningInConsole()) {
            if (!check_db_connection() || !\Schema::hasTable('plugins')) {
                $canAccessDB = false;
            }
        }

        if ($canAccessDB) {
            $plugins = $this->pluginRepository->get();
        }

        foreach (['core', 'plugins'] as $type) {
            $modules = get_folders_in_path(base_path($type));

            foreach ($modules as $row) {
                $file = $row . '/module.json';
                $data = json_decode(get_file_data($file), true);
                if ($data === null || !is_array($data)) {
                    continue;
                }

                if ($canAccessDB) {
                    if ($type === 'plugins') {
                        $plugin = $plugins->where('alias', '=', array_get($data, 'alias'))->first();

                        if (!$plugin) {
                            $result = $this->pluginRepository
                                ->create([
                                    'alias' => array_get($data, 'alias'),
                                    'enabled' => false,
                                    'installed' => false,
                                ]);
                            /**
                             * Everything ok
                             */
                            if ($result) {
                                $plugin = $this->pluginRepository->find($result);
                            }
                        }
                        if ($plugin) {
                            $data['enabled'] = !!$plugin->enabled;
                            $data['installed'] = !!$plugin->installed;
                            $data['id'] = $plugin->id;
                            $data['installed_version'] = $plugin->installed_version;
                        }
                    }
                }

                $modulesArr[array_get($data, 'namespace')] = array_merge($data, [
                    'file' => $file,
                    'type' => $type,
                ]);
            }
        }
        $this->modules = array_merge(get_base_vendor_modules_information(), $modulesArr);
        $this->modulesCollection = collect($this->modules);
        return $this->modules;
    }

    /**
     * @return array
     */
    public function getBaseVendorModules()
    {
        $modules = get_folders_in_path(base_path('vendor/sgsoft-studio'));
        $modulesArr = [];
        foreach ($modules as $module) {
            $file = $module . '/module.json';
            $data = json_decode(get_file_data($file), true);
            if ($data === null || !is_array($data)) {
                continue;
            }

            $modulesArr[array_get($data, 'namespace')] = array_merge($data, [
                'file' => $file,
                'type' => 'base',
            ]);
        }
        return $modulesArr;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getModulesByType($type)
    {
        if (!$this->modulesCollection) {
            $this->getAllModules();
        }

        return $this->modulesCollection
            ->where('type', '=', $type);
    }

    /**
     * @param $alias
     * @return mixed
     */
    public function findByAlias($alias)
    {
        if (!$this->modulesCollection) {
            $this->getAllModules();
        }

        return $this->modulesCollection
            ->where('alias', '=', $alias)
            ->first();
    }

    /**
     * @param $alias
     * @param array $data
     * @return bool
     */
    public function saveModule($alias, array $data)
    {
        $module = is_array($alias) ? $alias : get_module_information($alias);
        if (!$module || array_get($module, 'type') !== 'plugins') {
            return false;
        }

        return $this->pluginRepository
            ->createOrUpdate(array_get($module, 'id'), array_merge($data, [
                'alias' => array_get($module, 'alias'),
            ]));
    }
}
