<?php

namespace MadeTech\CacheWarming;


class CacheWarmer
{

    /** @var UseCase\WarmUpCacheForSite */
    private $warmUpCacheForSite;

    /** @var Config\ConfigProvider */
    private $configuration;

    public function __construct(UseCase\WarmUpCacheForSite $warmUpCacheForSite, Config\ConfigProvider $configuration)
    {
        $this->warmUpCacheForSite = $warmUpCacheForSite;
        $this->configuration = $configuration;
    }

    public function warmCaches(UseCase\WarmUpCacheForSitePresenter $presenter)
    {
        $siteMaps = $this->configuration->getSiteMapUrls();
        foreach ($siteMaps as $siteMap) {
            $this->warmUpCacheForSite->warmUpSiteCache($siteMap, $presenter);
        }
    }
}