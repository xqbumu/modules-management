<?php namespace WebEd\Base\ModulesManagement\Repositories;

use WebEd\Base\Core\Repositories\AbstractBaseRepository;
use WebEd\Base\Caching\Services\Contracts\CacheableContract;

use WebEd\Base\ModulesManagement\Repositories\Contracts\PluginsRepositoryContract;

class PluginsRepository extends AbstractBaseRepository implements PluginsRepositoryContract, CacheableContract
{
    protected $rules = [
        'alias' => 'string|max:255|alpha_dash',
        'installed_version' => 'string|max:255',
    ];

    protected $editableFields = [
        'alias',
        'installed_version',
        'enabled',
        'installed',
    ];

    /**
     * @param $alias
     * @return mixed
     */
    public function getByAlias($alias)
    {
        return $this->where('alias', '=', $alias)->first();
    }
}
