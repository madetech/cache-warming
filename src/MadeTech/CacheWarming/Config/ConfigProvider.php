<?php

namespace MadeTech\CacheWarming\Config;


interface ConfigProvider
{
    /** @var string */
    public function getSiteMapUrls();
}