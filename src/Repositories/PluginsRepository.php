<?php namespace WebEd\Base\ModulesManagement\Repositories;

use WebEd\Base\Caching\Services\Contracts\CacheableContract;
use WebEd\Base\Caching\Services\Traits\Cacheable;
use WebEd\Base\Core\Repositories\Eloquent\EloquentBaseRepository;
use WebEd\Base\ModulesManagement\Repositories\Contracts\PluginsRepositoryContract;

class PluginsRepository extends EloquentBaseRepository implements PluginsRepositoryContract, CacheableContract
{
    use Cacheable;

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
        return $this->model->where('alias', '=', $alias)->first();
    }
}
