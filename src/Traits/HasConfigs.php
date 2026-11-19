<?php

namespace Obelaw\Configs\Traits;

use Illuminate\Database\Eloquent\Builder;
use Obelaw\Configs\ConfigsManager;
use Obelaw\Configs\Services\ConfigurationService;

/**
 * Values that belong to a model, stored in o_configs under the model's group.
 */
trait HasConfigs
{
    /**
     * The group of this model's values: "{morph class}:{key}".
     */
    public function configGroup(): string
    {
        return $this->getMorphClass() . ':' . $this->getKey();
    }

    public function configs(): ConfigurationService
    {
        return app(ConfigsManager::class)->group($this->configGroup());
    }

    /**
     * This model's rows, e.g. `$model->allConfigs()->delete()`.
     */
    public function allConfigs(): Builder
    {
        return app(ConfigsManager::class)->query($this->configGroup());
    }
}
