<?php

namespace Realtyna\Core;

class Config
{
    protected array $config;

    public function __construct($configPath)
    {
        if (file_exists($configPath)) {
            $this->config = include $configPath;
        } else {
            throw new \Exception("Config file not found: $configPath");
        }
    }

    /**
     * Get a configuration value using a dot notation key.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $config = $this->config;

        foreach ($keys as $key) {
            if (isset($config[$key])) {
                $config = $config[$key];
            } else {
                return $default;
            }
        }

        return $config;
    }
}
