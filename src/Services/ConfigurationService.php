<?php

namespace Obelaw\Configs\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Obelaw\Configs\Models\Config;

/**
 * The values of one group (the global values when the group is null).
 *
 * Reads fall back to the global group (when given) and then to Laravel's
 * config(); writes always go to this group only.
 */
class ConfigurationService
{
    /**
     * All of the configuration items of this group.
     *
     * @var array
     */
    private $items = [];

    /**
     * @param  string|null  $group  Null for the global values.
     * @param  string|null  $connection  A fixed connection; null uses the model's own.
     * @param  self|null  $fallback  Where missing values are read from (the global group).
     */
    public function __construct(
        private ?string $group = null,
        private ?string $connection = null,
        private ?self $fallback = null,
    ) {
        $this->query()->get(['path', 'value'])->each(function (Config $item) {
            Arr::set($this->items, $item->path, $item->value);
        });
    }

    /**
     * The group these values belong to (null for the global values).
     */
    public function group(): ?string
    {
        return $this->group;
    }

    /**
     * Get all of the configuration items of this group.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Determine if this group has the given configuration value.
     *
     * @return bool
     */
    public function has(string $path)
    {
        return Arr::has($this->items, $path);
    }

    /**
     * Get the specified configuration value.
     *
     * @return mixed
     */
    public function get(string $path, $default = null)
    {
        if ($this->has($path)) {
            return Arr::get($this->items, $path);
        }

        if ($this->fallback) {
            return $this->fallback->get($path, $default);
        }

        return config($path, $default);
    }

    /**
     * Set a configuration value in this group.
     *
     * @return mixed
     */
    public function set(string $path, $value)
    {
        $this->newQuery()->updateOrCreate(
            ['group' => $this->group ?? '', 'path' => $path],
            ['value' => $value],
        );

        Arr::set($this->items, $path, $value);

        return $value;
    }

    /**
     * Forget a configuration value of this group.
     *
     * @return bool
     */
    public function forget(string $path)
    {
        if (! $this->has($path)) {
            return false;
        }

        Arr::forget($this->items, $path);

        return (bool) $this->query()->where('path', $path)->delete();
    }

    /**
     * The rows of this group.
     */
    public function query(): Builder
    {
        return $this->newQuery()->where('group', $this->group ?? '');
    }

    protected function newQuery(): Builder
    {
        return $this->connection ? Config::on($this->connection) : Config::query();
    }
}
