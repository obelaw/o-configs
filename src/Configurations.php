<?php

namespace Obelaw\Configuration;

use Obelaw\Configuration\Models\Configuration;

final class Configurations
{
    private array $settings = [];

    public function __construct()
    {
        $this->settings = Configuration::pluck('value', 'path')->toArray();
    }

    public function push($path, $value)
    {
        $this->settings[$path] = $value;
        return $this->settings;
    }

    public function set($path, $value)
    {
        $config = Configuration::updateOrCreate(['path' => $path], ['value' => $value]);

        if ($config)
            return $config->value;
    }

    public function get($path, $default = null)
    {
        if (isset($this->settings[$path])) {
            return $this->settings[$path];
        }

        return config($path, $default);
    }

    public function has($path)
    {
        if (isset($this->settings[$path]))
            return true;

        return false;
    }

    public function remove($path)
    {
        if ($setting = Configuration::wherePath($path)->first()) {
            return $setting->delete();
        }

        return false;
    }
}
