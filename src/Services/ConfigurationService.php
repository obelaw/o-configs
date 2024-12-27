<?php

namespace Obelaw\Configs\Services;

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
        $this->items = Config::all()->keyBy('path');
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function all()
    {
        return $this->items->pluck('value')->toArray();
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $path
     * @return boolean
     */
    public function has(string $path)
    {
        return $this->items->has($path);
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
            return app('config')->get($path, $default);
        }

        return $this->items->get($path)?->value;
    }

    /**
     * Forget a configuration value.
     *
     * @param string $path
     * @return boolean
     */
    public function forget(string $path)
    {
        if (!$this->items->has($path)) {
            return false;
        }

        unset($this->items[$path]);

        return $this->items->get($path)->delete();
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
        if ($this->has($path)) {
            $this->items->get($path)->update(compact('value'));
        } else {
            $this->items->put(
                $path,
                Config::create(compact('path', 'value'))
            );
        }

        return $value;
    }
}
