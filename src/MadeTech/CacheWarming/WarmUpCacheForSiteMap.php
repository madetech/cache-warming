<?php
namespace MadeTech\CacheWarming;

use MadeTech\CacheWarming\UseCase\WarmUpCacheForSitePresenter;

class WarmUpCacheForSiteMap implements UseCase\WarmUpCacheForSiteMap
{

    /** @var  UseCase\GetUrlsFromSiteMap */
    private $getUrlsFromSiteMap;

    /** @var  UseCase\WarmCacheOfOneUrl */
    private $warmCacheOfOneUrl;

    public function __construct(
        UseCase\GetUrlsFromSiteMap $getUrlsFromSiteMap,
        UseCase\WarmCacheOfOneUrl $warmCacheOfOneUrl
    ) {
        $this->getUrlsFromSiteMap = $getUrlsFromSiteMap;
        $this->warmCacheOfOneUrl = $warmCacheOfOneUrl;
    }

    public function warmUpSiteCache($siteMapUrl, WarmUpCacheForSitePresenter $presenter)
    {
        $urls = $this->getUrlsFromSiteMap->getUrlsFromSiteMap($siteMapUrl);
        foreach ($urls as $url) {
            $presenter->present($url);
            $this->warmCacheOfOneUrl->visit($url);
        }
    }
}
