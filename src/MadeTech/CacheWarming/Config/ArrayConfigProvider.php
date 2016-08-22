<?php

namespace MadeTech\CacheWarming\Config;


class ArrayConfigProvider implements ConfigProvider
{

    /** @var string[] */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /** @return string[] */
    public function getSiteMapUrls()
    {
        return $this->config;
    }
}