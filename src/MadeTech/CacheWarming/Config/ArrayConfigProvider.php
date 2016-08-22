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
        return array_values( array_filter($this->config, 'is_string') );
    }

    /** @return HrefLangExpansion[] */
    public function getHrefLangExpansions()
    {
        return array_values( array_filter($this->config, [$this, 'isHrefLangExpansion']) );
    }

    private function isHrefLangExpansion($item)
    {
        return $item instanceof HrefLangExpansion;
    }
}
