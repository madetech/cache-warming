<?php

namespace MadeTech\CacheWarming\Config;


interface ConfigProvider
{
    /** @return string[] */
    public function getSiteMapUrls();

    /** @return HrefLangExpansion[] */
    public function getHrefLangExpansions();
}