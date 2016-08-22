<?php
namespace MadeTech\CacheWarming;

use MadeTech\CacheWarming\UseCase\CacheWarmerPresenter;

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

    public function warmUpSiteCache($siteMapUrl, CacheWarmerPresenter $presenter)
    {
        $urls = $this->getUrlsFromSiteMap->getUrlsFromSiteMap($siteMapUrl);
        $presenter->presentSiteMapUrls( $siteMapUrl, $urls );
        foreach ($urls as $url) {
            $presenter->presentVisitedUrl($url);
            $this->warmCacheOfOneUrl->visit($url);
        }
    }
}
