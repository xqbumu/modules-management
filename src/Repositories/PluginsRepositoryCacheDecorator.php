<?php namespace WebEd\Base\ModulesManagement\Repositories;

use WebEd\Base\Caching\Repositories\AbstractRepositoryCacheDecorator;

use WebEd\Base\ModulesManagement\Repositories\Contracts\PluginsRepositoryContract;

class PluginsRepositoryCacheDecorator extends AbstractRepositoryCacheDecorator  implements PluginsRepositoryContract
{
    /**
     * @param $alias
     * @return mixed
     */
    public function getByAlias($alias)
    {
        return $this->beforeGet(__FUNCTION__, func_get_args());
    }
}
