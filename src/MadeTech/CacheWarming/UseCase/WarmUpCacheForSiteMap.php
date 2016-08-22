<?php
namespace MadeTech\CacheWarming\UseCase;

interface WarmUpCacheForSiteMap
{
    public function warmUpSiteCache($siteMapUrl, CacheWarmerPresenter $presenter);
}
