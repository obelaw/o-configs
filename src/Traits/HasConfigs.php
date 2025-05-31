<?php

namespace Obelaw\Configs\Traits;

use Obelaw\Configs\Models\ConfigModel;
use Obelaw\Configs\Services\ConfigurationModelService;

trait HasConfigs
{
    public function _configs()
    {
        return $this->morphMany(ConfigModel::class, 'modelable');
    }

    public function configs()
    {
        return new ConfigurationModelService($this->_configs());
    }
}
