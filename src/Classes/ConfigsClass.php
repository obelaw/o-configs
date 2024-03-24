<?php

namespace Obelaw\Configs\Classes;

use Obelaw\Configs\Models\Config;

final class ConfigsClass
{
    private array $settings = [];

    public function __construct()
    {
        $this->settings = Config::pluck('value', 'path')->toArray();
    }

    public function set($path, $value)
    {
        $config = Config::updateOrCreate(['path' => $path], ['value' => $value]);

        if ($config)
            return $config->value;
    }

    public function get($path, $default = null)
    {
        if (isset($this->settings[$path])) {
            return $this->settings[$path];
        }

        return app('config')->get($path, $default);
    }

    public function has($path)
    {
        if (isset($this->settings[$path]))
            return true;

        return false;
    }

    public function forget($path)
    {
        if ($setting = Config::wherePath($path)->first()) {
            return $setting->delete();
        }

        return false;
    }
}
