<?php

namespace Obelaw\Configs\Services;

use Illuminate\Support\Arr;
use Obelaw\Configs\Models\Config;

/**
 * @method array all() Get all of the configuration items.
 * @method boolean has(string $path) Determine if the given configuration value exists.
 * @method mixed get(string $path, $default = null) Get the specified configuration value.
 * @method boolean forget(string $path) Forget a configuration value.
 * @method mixed set(string $path, $value) Set a configuration value.
 */
class ConfigurationService
{
    /**
     * All of the configuration items.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $items;

    /**
     * Create a new configuration instance.
     */
    public function __construct()
    {
        Config::all()->each(function (Config $item) {
            Arr::set($this->items, $item->path, $item);
        });
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $path
     * @return boolean
     */
    public function has(string $path)
    {
        return Arr::has($this->items, $path);
    }

    /**
     * Get the specified configuration value.
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function get(string $path, $default = null)
    {
        if (!$this->has($path)) {
            return config($path, $default);
        }

        return Arr::get($this->items, $path);
    }

    /**
     * Forget a configuration value.
     *
     * @param string $path
     * @return boolean
     */
    public function forget(string $path)
    {
        if (!$this->has($path)) {
            return false;
        }

        Arr::forget($this->items, $path);

        return Config::wherePath($path)->delete();
    }

    /**
     * Set a configuration value.
     *
     * @param string $path
     * @param mixed $value
     * @return mixed
     */
    public function set(string $path, $value)
    {
        Config::updateOrCreate(compact('path'), compact('value'));

        Arr::set($this->items, $path, $value);

        return $value;
    }
}
