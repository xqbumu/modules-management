<?php namespace WebEd\Base\ModulesManagement\Models;

use WebEd\Base\ModulesManagement\Models\Contracts\PluginsModelContract;
use WebEd\Base\Core\Models\EloquentBase as BaseModel;

class Plugins extends BaseModel implements PluginsModelContract
{
    protected $table = 'plugins';

    protected $primaryKey = 'id';

    protected $fillable = [];

    public $timestamps = false;

    public function setEnabledAttribute($value)
    {
        $this->attributes['enabled'] = (int)!!$value;
    }

    public function setInstalledAttribute($value)
    {
        $this->attributes['installed'] = (int)!!$value;
    }
}
